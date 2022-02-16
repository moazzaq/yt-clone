<?php

namespace App\Console\Commands;

use FFMpeg\Format\Video\X264;
use Illuminate\Console\Command;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class VideoEncode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video-encode:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Video Encoding';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $low = (new X264('aac'))->setKiloBitrate(500);
        $high = (new X264('aac'))->setKiloBitrate(1000);

        FFMpeg::fromDisk('videos-temp')
            ->open('ZHd1XZRfhfxwgi6Q83dLk6h4VeWmMMmG8gcUXiHK.mp4')
            ->exportForHLS()
            ->addFormat($low,function ($filter){
              $filter->resize(640,480);
            })
            ->addFormat($high,function ($filter){
                $filter->resize(1280,720);
            })
            ->onProgress(function ($progress) {
             $this->info("Progress {$progress}%");
            })
            ->toDisk('videos-temp')
         //   ->inFormat(new \FFMpeg\Format\Audio\Aac)
            ->save('/test/file.m3u8');
    }
}
