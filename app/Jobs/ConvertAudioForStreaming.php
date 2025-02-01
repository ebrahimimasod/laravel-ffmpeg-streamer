<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Audio\Mp3;


class ConvertAudioForStreaming implements ShouldQueue
{
    use Queueable;

    private $mediaId;


    public function __construct($mediaId)
    {
        $this->mediaId = $mediaId;
    }


    public function handle(): void
    {
        $media = Media::query()->find($this->mediaId);
        $outputPath = storage_path("/app/private/hls/{$media->uuid}.m3u8");
        $lowBitrateFormat = (new Mp3())->setAudioKiloBitrate(64);
        $audioPath = storage_path($media->path);

        FFmpeg::fromDisk('local')
        ->open($audioPath)
            ->exportForHLS()
            ->addFormat($lowBitrateFormat)
            ->toDisk('local')
            ->save($outputPath);

        // با این کار، در کنار فایل m3u8 چندین فایل .ts هم تولید می‌شود
        // می‌توانید آن‌ها را در مسیر دلخواه قرار دهید

    }
}
