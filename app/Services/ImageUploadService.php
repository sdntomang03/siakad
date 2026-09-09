<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    /**
     * Mengunggah dan mengonversi gambar ke format WebP.
     */
    public function uploadAndConvertToWebp(UploadedFile $file, string $directory, ?string $oldFilePath = null): string
    {
        // 1. Hapus foto lama jika ada
        if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
        }

        // 2. Pastikan direktori tujuan sudah ada
        if (! Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // 3. Buat nama file unik dengan ekstensi .webp
        $filename = uniqid('foto_').'.webp';
        $path = trim($directory, '/').'/'.$filename;

        // 4. Inisialisasi ImageManager menggunakan GD Driver (API Intervention Image v4)
        $manager = ImageManager::usingDriver(Driver::class);
        $img = $manager->decode($file);

        // 5. Simpan gambar ke storage dengan konversi ke WebP (kualitas 80)
        Storage::disk('public')->put(
            $path,
            $img->encodeUsingFormat(Format::WEBP, quality: 80)
        );

        return $path;
    }
}
