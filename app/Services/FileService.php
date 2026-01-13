<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Save uploaded photo and automatically delete the old one if it exists.
     *
     * @param UploadedFile|null $file
     * @param string|null $oldPath
     * @param string $folder
     * @param string $disk
     * @return string|null
     */
    public function updateOrUpload(?UploadedFile $file, ?string $oldPath = null, string $folder = 'uploads', string $disk = 'public'): ?string
    {
        if (!$file) {
            return $oldPath;
        }

        $newPath = $file->store($folder, $disk);

        if ($newPath && $oldPath) {
            $this->delete($oldPath, $disk);
        }

        return $newPath;
    }

    /**
     * Delete photo from storage if exists
     *
     * @param string|null $path
     * @param string $disk
     * @return void
     */
    public function delete(string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
