<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user's profile page
     */
    public function show(): View
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $user = Auth::user();
            
            // Delete old profile image if it exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            // Store new profile image
            $imagePath = $request->file('profile_image')->store('profile-images', 'public');
            $data['profile_image'] = $imagePath;
        }

        Auth::user()->update($data);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}
