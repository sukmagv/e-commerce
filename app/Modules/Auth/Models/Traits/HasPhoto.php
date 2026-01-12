<?php

namespace App\Modules\Auth\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasPhoto
{
    /**
     * Save uploaded photo and automatically delete the old one if it exists.
     *
     * @param \Illuminate\Http\UploadedFile|null $file
     * @param string $folder
     * @param string $disk
     * @return string|null  The new file path
     */
    public function savePhoto(?UploadedFile $file, string $folder = 'uploads', string $disk = 'public'): ?string
    {
        if (!$file) {
            return $this->photo ?? null;
        }

        if (property_exists($this, 'photo') && $this->photo && Storage::disk($disk)->exists($this->photo)) {
            Storage::disk($disk)->delete($this->photo);
        }

        $newPath = $file->store($folder, $disk);

        $this->photo = $newPath;

        return $newPath;
    }

    /**
     * Delete photo from storage if exists
     *
     * @param string|null $disk
     * @return void
     */
    public function deletePhoto(string $disk = 'public'): void
    {
        if (property_exists($this, 'photo') && $this->photo && Storage::disk($disk)->exists($this->photo)) {
            Storage::disk($disk)->delete($this->photo);
            $this->photo = null;
        }
    }
}
