<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // 🟢 GET /api/users
    public function index()
    {
        return response()->json(User::all(), 200);
    }

    // 🟢 POST /api/users
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'image'    => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('user_images', 'public');
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'image'    => $imagePath,
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    // 🟢 GET /api/users/{id}
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

    // 🟢 PUT /api/users/{id}
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'image'    => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image); // delete old image
            }

            $user->image = $request->file('image')->store('user_images', 'public');
        }

        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'User updated', 'user' => $user], 200);
    }

    // 🟢 DELETE /api/users/{id}
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted'], 200);
    }
}
