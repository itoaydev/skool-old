<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show(string $id)
    {
        return User::find($id);
    }
    
    public function edit(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
        $user->update([
            'type' => $request['type'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
        ]);
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {

            $oldPicture = $user->image;
            if ($oldPicture) {
                Storage::disk('public')->delete($oldPicture);
            }
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('storage/photos'), $imageName); // Move image to public/storage/photos

            // Update user's image path in the database
            $user->image = 'photos/' . $imageName; // Update the image path in the database
            $user->save();

            return response()->json(['message' => 'Image uploaded successfully', 'user' => $user]);
        }

        return response()->json(['message' => 'No image provided']);
    }

}
