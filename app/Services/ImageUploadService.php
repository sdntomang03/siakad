<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageUploadService
{
    /**
     * Mengunggah dan mengonversi gambar ke format WebP.
     */
    public function uploadAndConvertToWebp(UploadedFile $file, string $directory, ?string $oldFilePath = null): string
    {
        // Hapus foto lama jika ada
        if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
        }

        // Buat nama file unik dengan ekstensi .webp
        $filename = uniqid('foto_').'.webp';
        $path = $directory.'/'.$filename;

        // Konversi dan simpan gambar menggunakan Intervention
        // Kualitas diatur ke 80% untuk kompresi yang optimal
        $image = Image::make($file)->encode('webp', 80);
        Storage::disk('public')->put($path, $image);

        return $path;
    }
}
