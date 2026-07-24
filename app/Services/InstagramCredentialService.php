<?php

namespace App\Services;

use App\Models\InstagramCredential;
use Illuminate\Support\Facades\Schema;

class InstagramCredentialService
{
    public function resolve(): array
    {
        $stored = $this->stored();

        if ($stored) {
            return [
                'access_token' => (string) $stored->access_token,
                'user_id' => (string) ($stored->user_id ?? ''),
                'page_id' => (string) ($stored->page_id ?? ''),
                'expires_at' => $stored->expires_at,
                'last_refreshed_at' => $stored->last_refreshed_at,
                'source' => 'database',
            ];
        }

        return [
            'access_token' => (string) config('services.instagram.access_token', ''),
            'user_id' => (string) config('services.instagram.user_id', ''),
            'page_id' => (string) config('services.instagram.page_id', ''),
            'expires_at' => null,
            'last_refreshed_at' => null,
            'source' => 'env',
        ];
    }

    public function accessToken(): string
    {
        return $this->resolve()['access_token'];
    }

    public function userId(): string
    {
        return $this->resolve()['user_id'];
    }

    public function pageId(): string
    {
        return $this->resolve()['page_id'];
    }

    public function applyToConfig(): void
    {
        $credentials = $this->resolve();

        if ($credentials['access_token'] !== '') {
            config(['services.instagram.access_token' => $credentials['access_token']]);
        }

        if ($credentials['user_id'] !== '') {
            config(['services.instagram.user_id' => $credentials['user_id']]);
        }

        if ($credentials['page_id'] !== '') {
            config(['services.instagram.page_id' => $credentials['page_id']]);
        }
    }

    public function store(array $data): InstagramCredential
    {
        $credential = InstagramCredential::query()->first() ?? new InstagramCredential;

        $credential->fill([
            'access_token' => $data['access_token'],
            'user_id' => $data['user_id'] ?? null,
            'page_id' => $data['page_id'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'last_refreshed_at' => $data['last_refreshed_at'] ?? now(),
        ]);

        $credential->save();

        $this->applyToConfig();

        return $credential;
    }

    public function syncFromEnvIfEmpty(): ?InstagramCredential
    {
        if ($this->stored()) {
            return null;
        }

        $accessToken = (string) config('services.instagram.access_token', '');

        if ($accessToken === '') {
            return null;
        }

        return $this->store([
            'access_token' => $accessToken,
            'user_id' => config('services.instagram.user_id'),
            'page_id' => config('services.instagram.page_id'),
            'last_refreshed_at' => now(),
        ]);
    }

    private function stored(): ?InstagramCredential
    {
        if (! Schema::hasTable('instagram_credentials')) {
            return null;
        }

        return InstagramCredential::query()->first();
    }
}
