<?php

namespace App\Services;

use App\Models\HomeSection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstagramFeedService
{
    public function getProfile(HomeSection $section): array
    {
        $feed = $this->getCachedFeed();

        if ($feed && ! empty($feed['profile'])) {
            return $feed['profile'];
        }

        $bodyLines = preg_split("/\r\n|\n|\r/", (string) $section->body);
        $handle = trim($bodyLines[0] ?? '@designdhaga');
        $bio = trim(implode("\n", array_slice($bodyLines, 1)));

        return [
            'name' => $section->title ?: 'Design Dhaga',
            'username' => $handle,
            'bio' => $bio,
            'avatar' => $section->image,
            'following' => null,
            'followers' => null,
            'media_count' => null,
        ];
    }

    public function getPosts(HomeSection $section): Collection
    {
        $feed = $this->getCachedFeed();

        if ($feed && ! empty($feed['posts'])) {
            return collect($feed['posts']);
        }

        return $this->getFallbackPosts($section);
    }

    private function getCachedFeed(): ?array
    {
        $token = config('services.instagram.access_token');

        if (! $token) {
            return null;
        }

        $cacheKey = 'instagram.feed';
        $cache = $this->instagramCache();
        $cached = $cache->get($cacheKey);

        if (is_array($cached)) {
            return $cached;
        }

        $feed = $this->fetchFromFacebookGraph($token) ?? $this->fetchFromInstagramGraph($token);

        if ($feed) {
            try {
                $cache->put($cacheKey, $feed, config('services.instagram.cache_ttl', 3600));
            } catch (\Throwable $exception) {
                Log::warning('Instagram feed: unable to cache API response.', [
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $feed;
    }

    private function instagramCache(): \Illuminate\Contracts\Cache\Repository
    {
        return Cache::store('file');
    }

    private function fetchFromFacebookGraph(string $token): ?array
    {
        try {
            $version = config('services.instagram.graph_version', 'v21.0');
            $igUserId = config('services.instagram.user_id');
            $accessToken = $token;

            if ($igUserId) {
                $feed = $this->fetchInstagramBusinessFeed($version, $igUserId, $accessToken);

                if ($feed) {
                    return $feed;
                }
            }

            $pagesResponse = Http::timeout(20)
                ->retry(2, 250)
                ->get("https://graph.facebook.com/{$version}/me/accounts", [
                    'fields' => 'instagram_business_account,access_token',
                    'access_token' => $token,
                ]);

            if (! $pagesResponse->successful()) {
                Log::warning('Instagram feed: unable to load Facebook pages.', [
                    'status' => $pagesResponse->status(),
                    'body' => $pagesResponse->json(),
                ]);

                return null;
            }

            $page = collect($pagesResponse->json('data', []))
                ->first(fn ($item) => ! empty($item['instagram_business_account']['id']));

            if (! $page) {
                Log::warning('Instagram feed: no Facebook page linked to an Instagram business account.');

                return null;
            }

            $igUserId = $page['instagram_business_account']['id'];
            $accessToken = $page['access_token'] ?? $token;

            return $this->fetchInstagramBusinessFeed($version, $igUserId, $accessToken);
        } catch (\Throwable $exception) {
            Log::warning('Instagram feed: Facebook Graph request failed.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function fetchInstagramBusinessFeed(string $version, string $igUserId, string $accessToken): ?array
    {
        $profileResponse = Http::timeout(20)
            ->retry(2, 250)
            ->get("https://graph.facebook.com/{$version}/{$igUserId}", [
                'fields' => 'username,name,biography,profile_picture_url,followers_count,follows_count,media_count',
                'access_token' => $accessToken,
            ]);

        if (! $profileResponse->successful()) {
            Log::warning('Instagram feed: unable to load Instagram profile.', [
                'status' => $profileResponse->status(),
                'body' => $profileResponse->json(),
            ]);

            return null;
        }

        $profileData = $profileResponse->json();

        $mediaResponse = Http::timeout(20)
            ->retry(2, 250)
            ->get("https://graph.facebook.com/{$version}/{$igUserId}/media", [
                'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp,children{media_url,thumbnail_url,media_type}',
                'limit' => config('services.instagram.post_limit', 20),
                'access_token' => $accessToken,
            ]);

        if (! $mediaResponse->successful()) {
            Log::warning('Instagram feed: unable to load Instagram media.', [
                'status' => $mediaResponse->status(),
                'body' => $mediaResponse->json(),
            ]);

            return null;
        }

        $posts = collect($mediaResponse->json('data', []))
            ->map(fn (array $item) => $this->normalizeMediaItem($item))
            ->filter(fn ($post) => ! empty($post['media_url']))
            ->values()
            ->all();

        if ($posts === []) {
            Log::warning('Instagram feed: media endpoint returned no usable posts.');

            return null;
        }

        return [
            'profile' => [
                'name' => $profileData['name'] ?? $profileData['username'] ?? 'Design Dhaga',
                'username' => '@' . ltrim((string) ($profileData['username'] ?? 'designdhaga'), '@'),
                'bio' => (string) ($profileData['biography'] ?? ''),
                'avatar' => $profileData['profile_picture_url'] ?? null,
                'following' => $profileData['follows_count'] ?? null,
                'followers' => $profileData['followers_count'] ?? null,
                'media_count' => $profileData['media_count'] ?? null,
            ],
            'posts' => $posts,
        ];
    }

    private function fetchFromInstagramGraph(string $token): ?array
    {
        try {
            $profileResponse = Http::timeout(15)->get('https://graph.instagram.com/me', [
                'fields' => 'id,username,account_type,media_count,biography,profile_picture_url',
                'access_token' => $token,
            ]);

            if (! $profileResponse->successful()) {
                return null;
            }

            $profileData = $profileResponse->json();
            $userId = $profileData['id'] ?? null;

            if (! $userId) {
                return null;
            }

            $mediaResponse = Http::timeout(15)->get("https://graph.instagram.com/{$userId}/media", [
                'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp',
                'limit' => config('services.instagram.post_limit', 20),
                'access_token' => $token,
            ]);

            if (! $mediaResponse->successful()) {
                return null;
            }

            $posts = collect($mediaResponse->json('data', []))
                ->map(fn (array $item) => $this->normalizeMediaItem($item))
                ->filter(fn ($post) => ! empty($post['media_url']))
                ->values()
                ->all();

            return [
                'profile' => [
                    'name' => $profileData['username'] ?? 'Design Dhaga',
                    'username' => '@' . ltrim((string) ($profileData['username'] ?? 'designdhaga'), '@'),
                    'bio' => (string) ($profileData['biography'] ?? ''),
                    'avatar' => $profileData['profile_picture_url'] ?? null,
                    'following' => null,
                    'followers' => null,
                    'media_count' => $profileData['media_count'] ?? null,
                ],
                'posts' => $posts,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeMediaItem(array $item): array
    {
        $mediaType = strtoupper((string) ($item['media_type'] ?? 'IMAGE'));
        $imageUrl = $item['thumbnail_url'] ?? $item['media_url'] ?? null;

        if (! $imageUrl && ! empty($item['children']['data'][0])) {
            $child = $item['children']['data'][0];
            $imageUrl = $child['thumbnail_url'] ?? $child['media_url'] ?? null;
            $mediaType = strtoupper((string) ($child['media_type'] ?? $mediaType));
        }

        return [
            'id' => (string) ($item['id'] ?? ''),
            'caption' => (string) ($item['caption'] ?? ''),
            'media_type' => $mediaType,
            'media_url' => $imageUrl,
            'thumbnail_url' => $imageUrl,
            'permalink' => $item['permalink'] ?? null,
            'alt' => $this->captionAltText((string) ($item['caption'] ?? '')),
        ];
    }

    private function getFallbackPosts(HomeSection $section): Collection
    {
        return $section->items
            ->where('status', 1)
            ->sortBy('sort_order')
            ->values()
            ->map(fn ($item) => [
                'id' => (string) $item->id,
                'caption' => (string) ($item->subtitle ?: $item->body ?: $item->title),
                'media_type' => strtoupper((string) ($item->icon ?: 'IMAGE')),
                'media_url' => $item->image,
                'thumbnail_url' => $item->image,
                'permalink' => $item->link_url,
                'alt' => $item->alt_tag ?: $item->title ?: 'Instagram post',
            ]);
    }

    private function captionAltText(string $caption): string
    {
        $caption = trim(preg_replace('/\s+/', ' ', $caption));

        if ($caption === '') {
            return 'Design Dhaga Instagram post';
        }

        return strlen($caption) > 120 ? substr($caption, 0, 117) . '...' : $caption;
    }
}
