@if($instagramFeed && $instagramPosts->isNotEmpty())
@php
    $followUrl = $instagramFeed->button_url ?: ($settings->instagram ?? 'https://www.instagram.com/design.dhaga');
@endphp
<section class="instagram-feed-section py-3" id="instagram-feed">
    <div class="container-fluid">
        <div class="instagram-feed-header text-center mb-4">
            <h2 class="instagram-feed-title mb-2">
                #designdhaga on Instagram
                <i class="fa-brands fa-instagram instagram-feed-icon" aria-hidden="true"></i>
            </h2>
            @if($instagramFeed->subtitle)
                <p class="instagram-feed-subtitle mb-0">{{ $instagramFeed->subtitle }}</p>
            @endif
        </div>

        <div class="instagram-profile-bar d-flex align-items-center flex-wrap gap-3 mb-4">
            @php
                $avatarUrl = $instagramProfile['avatar'] ?? '';
                $avatarIsExternal = str_starts_with($avatarUrl, 'http://') || str_starts_with($avatarUrl, 'https://');
                $avatarImage = $avatarUrl && ! $avatarIsExternal
                    ? responsiveImage($avatarUrl, [80, 120, 160])
                    : null;
            @endphp

            <div class="instagram-profile-avatar-wrap">
                @if($avatarIsExternal)
                    <img
                        src="{{ $avatarUrl }}"
                        alt="{{ $instagramProfile['name'] }} profile picture"
                        class="instagram-profile-avatar"
                        loading="lazy"
                        decoding="async"
                    >
                @elseif($avatarImage)
                    <img
                        src="{{ $avatarImage['src'] }}"
                        srcset="{{ $avatarImage['srcset'] }}"
                        sizes="72px"
                        alt="{{ $instagramProfile['name'] }} profile picture"
                        class="instagram-profile-avatar"
                        loading="lazy"
                        decoding="async"
                    >
                @else
                    <div class="instagram-profile-avatar instagram-profile-avatar--placeholder">
                        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                    </div>
                @endif
            </div>

            <div class="instagram-profile-info flex-grow-1">
                <div class="instagram-profile-heading">
                    <span class="instagram-profile-name">{{ $instagramProfile['name'] }}</span>
                    @if($instagramProfile['username'])
                        <span class="instagram-profile-handle">{{ $instagramProfile['username'] }}</span>
                    @endif
                </div>

                @if($instagramProfile['bio'])
                    <p class="instagram-profile-bio mb-2">{{ $instagramProfile['bio'] }}</p>
                @endif
            </div>

            <a
                href="{{ $followUrl }}"
                class="btn instagram-follow-btn"
                target="{{ $instagramFeed->button_target ?: '_blank' }}"
                rel="noopener noreferrer"
            >
                {{ $instagramFeed->button_text ?: 'Follow on Instagram' }}
            </a>
        </div>

        <div id="instagramFeedSlider">
            <div class="owl-carousel owl-theme instagram-feed-carousel">
                @foreach($instagramPosts as $post)
                    @php
                        $mediaUrl = $post['media_url'] ?? '';
                        $isExternalMedia = str_starts_with($mediaUrl, 'http://') || str_starts_with($mediaUrl, 'https://');
                        $postImage = $isExternalMedia
                            ? ['src' => $mediaUrl, 'srcset' => '']
                            : ['src' => asset($mediaUrl), 'srcset' => ''];
                        $postUrl = $post['permalink'] ?: $followUrl;
                        $isVideo = in_array(strtoupper((string) ($post['media_type'] ?? '')), ['VIDEO', 'REELS'], true);
                    @endphp
                    <div class="instagram-feed-item">
                        <a href="{{ $postUrl }}" class="instagram-feed-media" target="_blank" rel="noopener noreferrer">
                            <img
                                src="{{ $postImage['src'] }}"
                                @if($postImage['srcset']) srcset="{{ $postImage['srcset'] }}" @endif
                                sizes="(max-width: 767px) 33vw, 20vw"
                                alt="{{ $post['alt'] ?? 'Instagram post' }}"
                                class="instagram-feed-image skip-lazy"
                                loading="eager"
                                decoding="async"
                                data-no-lazy="1"
                                fetchpriority="low"
                            >
                            @if($isVideo)
                                <span class="instagram-feed-play" aria-hidden="true">
                                    <i class="fas fa-play"></i>
                                </span>
                            @endif
                        </a>
                        @if(! empty($post['caption']))
                            <p class="instagram-feed-caption">{{ $post['caption'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif