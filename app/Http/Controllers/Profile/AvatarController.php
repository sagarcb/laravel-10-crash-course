<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

class AvatarController extends Controller
{
    public function update(UpdateAvatarRequest $request) {

        $path = Storage::disk('public')->put('avatars', $request->file('avatar'));

        if ($oldAvatar = $request->user()->avatar) {
            Storage::disk('public')->delete($oldAvatar);
        }
        auth()->user()->update(['avatar' => $path]);
        return redirect(route('profile.edit'))->with('message', 'Avatar is updated');
    }

    public function generateAvatar() {

        $result = OpenAI::Images()->create([
            "prompt" => "animated boy avatar with stylish whose name is " . auth()->user()->name ,
            "n" => 1,
            "size" => "256x256"
        ]);
        $content = file_get_contents($result['data'][0]['url']);
        $filename = Str::random(25) . '.jpg';
        Storage::disk('public')->put("avatars/$filename", $content);

        if ($oldAvatar = auth()->user()->avatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        auth()->user()->update(['avatar' => "avatars/$filename"]);
        return redirect(route('profile.edit'))->with('message', 'Avatar is updated');
    }
}
