<div>
    <div class="d-flex gray-text">
        <div class="d-flex align-items-center">
            <span class="material-icons {{$likeActive ? 'text-primary' : ''}}" style="font-size:2rem; cursor: pointer" wire:click.prevent="likes">thumb_up</span>
            <span class="mx-2">{{$likes}}</span>
        </div>

        <div class="d-flex align-items-center">
            <span class="material-icons {{$dislikeActive ? 'text-primary' : ''}}" style="font-size:2rem; cursor: pointer" wire:click.prevent="dislikes">thumb_down</span>
            <span class="mx-2">{{$dislikes}}</span>
        </div>
    </div>
</div>
