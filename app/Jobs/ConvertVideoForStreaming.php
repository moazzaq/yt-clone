<?php

namespace App\Jobs;

use App\Models\Video;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertVideoForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $path = '/' . $this->video->uid . '/' . $this->video->uid . '.m3u8';
        $low = (new X264('aac'))->setKiloBitrate(500);
        $high = (new X264('aac'))->setKiloBitrate(1000);

        FFMpeg::fromDisk('videos-temp')
            ->open($this->video->path)
            ->exportForHLS()
            ->addFormat($low,function ($filter){
                $filter->resize(640,480);
            })
            ->addFormat($high,function ($filter){
                $filter->resize(1280,720);
            })
            ->onProgress(function ($progress) {
                $this->video->update([
                   'processing_percentage' => $progress
                ]);
            })
            ->toDisk('videos')
            //   ->inFormat(new \FFMpeg\Format\Audio\Aac)
            ->save($path);

        $this->video->update([
            'processed' => true,
            'processed_file' => $this->video->uid . '.m3u8',
        ]);

        // delete temp-videos
        Storage::disk('videos-temp')->delete($this->video->path);
    }
}
