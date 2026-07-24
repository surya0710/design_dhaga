<?php

namespace App\Console\Commands;

use App\Services\InstagramCredentialService;
use App\Services\InstagramTokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DiagnoseInstagramLink extends Command
{
    protected $signature = 'instagram:diagnose-link';

    protected $description = 'Diagnose Facebook Page to Instagram account linkage and API permissions';

    public function handle(
        InstagramCredentialService $credentials,
        InstagramTokenService $tokens
    ): int {
        $credentials->syncFromEnvIfEmpty();
        $credentials->applyToConfig();

        $token = $credentials->accessToken();

        if ($token === '') {
            $this->error('No Instagram access token found.');

            return self::FAILURE;
        }

        $link = $tokens->resolveLinkedInstagramAccount(
            $token,
            $credentials->pageId() ?: null,
            $credentials->userId() ?: null
        );

        $this->info('Token type: ' . ($link['token_type'] ?? 'unknown'));
        $this->line('Token valid: ' . (($link['token_valid'] ?? false) ? 'yes' : 'no'));
        $this->line('Facebook page ID: ' . ($link['page_id'] ?: 'not found'));
        $this->line('Facebook page name: ' . ($link['page_name'] ?: 'unknown'));
        $this->line('Saved INSTAGRAM_USER_ID: ' . ($credentials->userId() ?: 'not set'));
        $this->line('Linked Instagram ID: ' . ($link['instagram_user_id'] ?: 'not linked'));
        $this->line('Linked Instagram username: ' . ($link['instagram_username'] ? '@' . $link['instagram_username'] : 'not linked'));

        if (! empty($link['scopes'])) {
            $this->newLine();
            $this->info('Token permissions:');
            foreach ($link['scopes'] as $scope) {
                $this->line(' - ' . $scope);
            }
        }

        if ($link['linked'] && ! empty($link['instagram_user_id'])) {
            $version = config('services.instagram.graph_version', 'v21.0');
            $media = Http::timeout(20)->get("https://graph.facebook.com/{$version}/{$link['instagram_user_id']}/media", [
                'fields' => 'id,caption,media_type,permalink',
                'limit' => 3,
                'access_token' => $token,
            ]);

            $this->newLine();
            $this->line('Media API status: ' . $media->status());

            if ($media->successful()) {
                $count = count($media->json('data', []));
                $this->info("Live Instagram media accessible ({$count} posts returned in test).");
                $this->comment('Run: php artisan instagram:refresh-token --save');

                return self::SUCCESS;
            }

            $this->error($media->body());
            $link['errors'][] = 'Instagram account is linked but media API failed. Add instagram_basic permission and regenerate token.';
        }

        $this->newLine();
        $this->error('Instagram feed cannot use live API yet.');

        foreach ($link['errors'] as $error) {
            $this->warn($error);
        }

        $this->newLine();
        $this->comment('Fix checklist:');
        $this->line('1. Meta Business Suite -> link Facebook Page to Instagram Professional account');
        $this->line('2. Graph API Explorer -> generate token with instagram_basic, pages_show_list, pages_read_engagement');
        $this->line('3. Remove stale INSTAGRAM_USER_ID from .env if page linkage changed');
        $this->line('4. Run: php artisan instagram:refresh-token --save');

        return self::FAILURE;
    }
}
