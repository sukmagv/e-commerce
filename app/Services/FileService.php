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
    public function updateOrCreate(?UploadedFile $file, ?string $oldFilename = null, string $folder = 'uploads', string $disk = 'public'): ?string
    {
        if (!$file) {
            return $oldFilename;
        }

        $filename = $file->hashName();

        $file->storeAs($folder, $filename, $disk);

        if ($oldFilename) {
            $this->delete($oldFilename, $disk);
        }

        return $filename;
    }

    /**
     * Delete photo from storage if exists
     *
     * @param string|null $path
     * @param string $disk
     * @return void
     */
    public function delete(string $filename, string $disk): void
    {
        $path = str_replace(url('/storage') . '/', '', $filename);

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
