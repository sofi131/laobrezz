<?php
namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(Request $request): User
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if(! empty($request->roles)) {
            $user->assignRole($request->roles);
        }
        return $user;
    }

    public function assignRole(Request $request, User $user): void
    {
        $roles = $request->roles ?? [];
        $user->assignRole($roles);
    }
}