<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;


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
        $mp3Path = Storage::disk('local')->path($media->path);
//        $hlsPath = Storage::disk('local')->path("files/$media->uuid/hls/playlist.m3u8");
//        $command = "C:/ffmpeg/bin/ffmpeg.exe -y -fflags +genpts -i {$mp3Path}  -c:a aac -b:a 320k  -avoid_negative_ts make_zero  -hls_time 10  -hls_playlist_type vod  -hls_flags independent_segments    -f hls    -hls_segment_filename \"{$hlsPath}/track_%05d.ts\"  {$hlsPath}/playlist.m3u8";

//        exec($command);
//        //C:/ffmpeg/bin/ffmpeg.exe -y -fflags +genpts -i "D:/projects/downloader-app/storage/app/private/uploads/input.mp3"    -c:a aac -b:a 320k    -avoid_negative_ts make_zero   -hls_time 10   -hls_playlist_type vod    -hls_flags independent_segments    -f hls    -hls_segment_filename "D:/projects/downloader-app/storage/app/private/hls/segments/track_%05d.ts"    "D:/projects/downloader-app/storage/app/private/hls/playlist.m3u8

        FFmpeg::fromDisk('local')
            ->open($media->path)
            ->exportForHLS()
            ->setSegmentLength(10)
            ->addFormat((new \FFMpeg\Format\Audio\Mp3())->setAudioKiloBitrate(320))
            ->save("files/$media->uuid/hls/playlist.m3u8");


    }
}
