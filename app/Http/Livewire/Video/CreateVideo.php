<?php

namespace App\Http\Livewire\Video;

use App\Jobs\ConvertVideoForStreaming;
use App\Jobs\CreateThumbnailForVideo;
use Livewire\WithFileUploads;
use App\Models\Channel;
use App\Models\Video;
use Livewire\Component;

class CreateVideo extends Component
{

    use WithFileUploads;
    public $channel;
    public $video;
    public $videoFile;

    protected $rules = [
        'videoFile' => 'required|mimes:mp4|max:1228800'
    ];

    public function mount(Channel $channel)
    {
        $this->channel = $channel;
    }
    public function render()
    {
        return view('livewire.video.create-video')
            ->extends('layouts.app');
    }

    public function fileCompleted()
    {
        $this->validate();

        $path = $this->videoFile->store('videos-temp');

        $this->video = $this->channel->videos()->create([
            'title' => 'untitled',
            'description' => 'none',
            'uid' => uniqid(true),
            'visibility' => 'private',
            'path' => explode('/',$path)[1],
        ]);

        // Dispatching Jobs
        CreateThumbnailForVideo::dispatch($this->video);
        ConvertVideoForStreaming::dispatch($this->video);

        //redirect to edit route
        return redirect()->route('video.edit', [
            'channel' => $this->channel,
            'video' => $this->video,
        ]);
    }

}
