<?php

namespace App\Http\Livewire\Video;

use App\Models\Dislike;
use App\Models\Like;
use App\Models\Video;
use Livewire\Component;

class Voting extends Component
{

    public $video;
    public $likes, $dislikes, $likeActive, $dislikeActive;

    protected $listeners = ['load_values' => '$refresh'];

    public function mount(Video $video)
    {
        $this->video = $video;
        $this->checkIfLike();
        $this->checkIfDislike();
    }

    public function checkIfLike()
    {
        $this->video->doesUserLikedVideo() ? $this->likeActive = true : $this->likeActive = false;
    }

    public function checkIfDislike()
    {
        $this->video->doesUserDislikedVideo() ? $this->dislikeActive = true : $this->dislikeActive = false;
    }

    public function render()
    {
        $this->likes = $this->video->likes->count();
        $this->dislikes = $this->video->dislikes->count();

        return view('livewire.video.voting');
    }

    public function likes()
    {
        // check if user already like the video
        if ($this->video->doesUserLikedVideo()) {

            Like::where('user_id',auth()->id())->where('video_id',$this->video->id)->delete();

            $this->likeActive = false;
        } else {
            $this->video->likes()->create([
                'user_id' => auth()->id()
            ]);
            $this->likeActive = true;
            $this->disableDislike();
            Dislike::where('user_id',auth()->id())->where('video_id',$this->video->id)->delete();
        }
        $this->emit('load_values');

    }


    public function dislikes()
    {

        // check if user already disliked the video
        if ($this->video->doesUserDislikedVideo()){
            Dislike::where('user_id',auth()->id())->where('video_id',$this->video->id)->delete();
            $this->dislikeActive = false;
        }else{
            $this->video->dislikes()->create([
                'user_id' => auth()->id()
            ]);
            $this->dislikeActive = true;
            $this->disableLike();
        }
        $this->emit('load_values');

    }

    public function disableDislike()
    {
        Dislike::where('user_id',auth()->id())->where('video_id',$this->video->id)->delete();
        $this->dislikeActive = false;
    }

    public function disableLike()
    {
        Like::where('user_id',auth()->id())->where('video_id',$this->video->id)->delete();
        $this->likeActive = false;
    }
}
