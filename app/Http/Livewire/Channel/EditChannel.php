<?php

namespace App\Http\Livewire\Channel;

use App\Models\Channel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditChannel extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public $channel, $image;

    protected function rules()
    {
        return [
            'channel.name' => 'required|max:255|unique:channels,name,' . $this->channel->id,
            'channel.slug' => 'required|max:255|unique:channels,slug,' . $this->channel->id,
            'channel.description' => 'nullable|max:1000',
            'image' => 'nullable|image|max:1024',
        ];
    }

    public function mount(Channel $channel)
    {
        $this->authorize('update', $this->channel);
        $this->channel = $channel;
    }

    public function render()
    {
        return view('livewire.channel.edit-channel');
    }

    public function update()
    {
        $this->authorize('update', $this->channel);

        $this->validate();

        $this->channel->update([

            'name' => $this->channel->name,
            'slug' => $this->channel->name,
            'description' => $this->channel->description,
          //  'image' => $this->channel->image->store('image'),

        ]);

        if ($this->image){
            $image = $this->image->storeAs('images', $this->channel->uid . '.png');
            $imageImage = explode('/', $image)[1];
            //resize and convert to png
            $img = Image::make(storage_path() . '/app/'  . $image)
                ->encode('png')
                ->fit(80, 80, function ($constraint) {
                    $constraint->upsize();
                })->save();

            $this->channel->update([
                'image' => $imageImage,
            ]);
        }

        session()->flash('message', 'Channel updated');

        return redirect()->route('channel.edit', ['channel' => $this->channel->slug]);
    }
}
