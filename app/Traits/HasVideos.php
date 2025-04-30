<?php

namespace App\Traits;

use App\Jobs\ProcessVideoJob;
use App\Models\ProductImage;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait HasVideos
{
    /**
     * Stores the product images.
     */
    public function storeProductVideo($product, $video)
    {
         // رفع الفيديو
         $originalVideoPath = $video->store('videos/original', 'public');

         // تحديد مسارات الملفات
         $compressedVideoPath = 'videos/compressed/' . basename($originalVideoPath);
         $thumbnailPath = 'videos/thumbnails/' . pathinfo($originalVideoPath, PATHINFO_FILENAME) . '.jpg';
 
         // معالجة الفيديو باستخدام FFmpeg
         ProcessVideoJob::dispatch(
             storage_path('app/public/' . $originalVideoPath),
             storage_path('app/public/' . $compressedVideoPath),
             storage_path('app/public/' . $thumbnailPath)
         );

         return [
            'original_video' => Storage::url($originalVideoPath),
            'compressed_video' => Storage::url($compressedVideoPath),
            'thumbnail' => Storage::url($thumbnailPath),
         ];
    }

    /**
     * Processes a video by compressing it and extracting a thumbnail.
     */
    private function processVideo($originalPath, $compressedPath, $thumbnailPath)
    {
        // إنشاء كائن FFmpeg
        $ffmpeg = FFMpeg::create();

        // فتح الفيديو
        $video = $ffmpeg->open($originalPath);

        // استخراج صورة مصغرة
        $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(1))
              ->save($thumbnailPath);

        // ضغط الفيديو وحفظه
        $video->save(new X264(), $compressedPath);
    }
}
