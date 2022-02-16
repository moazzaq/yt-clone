<?php

namespace App\Http\Livewire;

use App\Models\Video;
use Livewire\Component;

class WatchVideo extends Component
{
    public $video;

    protected $listeners = ['VideoViewed' => 'countView'];

    public function mount(Video $video)
    {
        $this->video = $video;
    }

    public function render()
    {
        return view('livewire.watch-video')->extends('layouts.app');
    }

    public function countView()
    {
      //  $this->video->increment('views');

         $this->video->update([
            'views' => $this->video->views + 1
        ]);

      //  session()->put('videoIsVisited',$this->video->uid);
    }
}
