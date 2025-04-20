<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Profile',
            'list'  => ['Home', 'Profile']
        ];

        $page = (object) [
            'title' => 'Profile'
        ];

        return view('profile.index', ['user' => Auth::user(), 'breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => '']);
    }

    public function edit()
    {
        $breadcrumb = (object) [
            'title' => 'Profile',
            'list'  => ['Home', 'Profile']
        ];

        $page = (object) [
            'title' => 'Profile'
        ];

        return view('profile.edit', ['user' => Auth::user(), 'breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => '']);
    }

    public function update(Request $request, string $id)
    {
        $user = UserModel::find($id);

        if ($user->profile_photo_url) {
            Storage::disk('public')->delete($user->profile_photo_url);
        }

        if ($request->hasFile('profile_photo')) {
            $request->validate([
                'profile_photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_url = $path;
            $user->save();
        }

        return response()->json(['message' => 'Foto berhasil diperbarui']);
    }

    public function delete(string $id)
    {
        $user = UserModel::find($id);
        
        if ($user->profile_photo_url) {
            Storage::disk('public')->delete($user->profile_photo_url);
        }

        $user->profile_photo_url = null;
        $user->save();

        return response()->json(['message' => 'Foto berhasil dihapus']);
    }
}
