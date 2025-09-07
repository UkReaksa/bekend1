<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role; // Import the Role model
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function index()
    {
        // Return all users with their roles eager-loaded
        return response()->json(User::get(), 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            // Validate that 'roles' is an array and each ID exists in the 'roles' table
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Attach the roles to the new user if they are provided
        if ($request->has('roles')) {
            $user->roles()->attach($request->roles);
        }

        // Return the new user with their roles loaded
        return response()->json($user->load('roles'), 201);
    }


    public function show(User $user)
    {
        // Use route model binding and return the user with their roles loaded
        return response()->json($user->load('roles'), 200);
    }


    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'sometimes|string|min:8',
            // Validate roles for updating
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the main user details
        $user->update($request->only('name', 'email'));

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        // in the array and adds any new ones.
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        $user->save();

        return response()->json($user->load('roles'), 200);
    }


    public function destroy(User $user)
    {
        // Detach all roles from the user before deleting
        $user->roles()->detach();
        $user->delete();

        return response()->json(null, 204);
    }
}
