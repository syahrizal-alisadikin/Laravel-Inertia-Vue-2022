<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        //get users
        $users = User::when(request()->q, function ($users) {
            $users = $users->where('name', 'like', '%' . request()->q . '%');
        })->with('roles')->latest()->paginate(5);

        //return inertia
        return Inertia::render('Apps/Users/Index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        //get roles
        $roles = Role::all();

        //return inertia
        return Inertia::render('Apps/Users/Create', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        /**
         * Validate request
         */
        $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);

        /**
         * Create user
         */
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //assign roles to user
        $user->assignRole($request->roles);

        //redirect
        return redirect()->route('apps.users.index');
    }

    public function edit($id)
    {
        //get user
        $user = User::with('roles')->findOrFail($id);

        //get roles
        $roles = Role::all();

        //return inertia
        return Inertia::render('Apps/Users/Edit', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    public function update(Request $request, User $user)
    {
        // dd($user->id);
        /**
         * validate request
         */
        $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed'
        ]);

        /**
         * check password is empty
         */

        $user->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password != null ? bcrypt($request->password) : $user->password
        ]);

        //assign roles to user
        $user->syncRoles($request->roles);

        //redirect
        return redirect()->route('apps.users.index');
    }

    public function destroy($id)
    {
        //find user
        $user = User::findOrFail($id);

        //delete user
        $user->delete();

        //redirect
        return redirect()->route('apps.users.index');
    }
}
