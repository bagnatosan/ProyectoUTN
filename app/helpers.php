<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('storage_url')) {
    /**
     * Returns the public URL for a stored file.
     * Uses the r2 disk if configured, otherwise falls back to local public storage.
     */
    function storage_url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $disk = config('filesystems.default', 'public');

        if ($disk === 'r2' || env('CLOUDFLARE_R2_ACCESS_KEY_ID')) {
            return Storage::disk('r2')->url($path);
        }

        return asset('storage/' . $path);
    }
}
