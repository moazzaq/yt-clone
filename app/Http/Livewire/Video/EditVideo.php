<?php

namespace App\Http\Livewire\Video;

use App\Models\Channel;
use App\Models\Video;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class EditVideo extends Component
{
    use AuthorizesRequests;

    public $channel;
    public $video;

    protected $rules = [
        'video.title' => 'required|max:255',
        'video.description' => 'nullable|max:1000',
        'video.visibility' => 'required|in:private,public,unlisted',
    ];

    public function mount(Channel $channel, Video $video)
    {
        $this->authorize('update', $this->channel);
        $this->channel = $channel;
        $this->video = $video;
    }

    public function render()
    {
        return view('livewire.video.edit-video')
            ->extends('layouts.app');
    }

    public function update()
    {
        $this->validate();
        //update video record
        $this->video->update([
            'title' => $this->video->title,
            'description' => $this->video->description,
            'visibility' => $this->video->visibility
        ]);

        session()->flash('message', 'video was update ');
    }
}
