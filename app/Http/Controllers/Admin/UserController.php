<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\EditUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "لیست کاربران";
        return view('admin.users.list', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = "ایجاد کاربر";
        return view('admin.users.create',compact('title'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(createUserRequest $request)
    {
        User::createUser($request);
        return redirect()->route('users.index')->with('message', 'کاربر جدید با موفقیت ایجاد شد');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = "ویرایش کاربر";
        $user = User::query()->find($id);
        return view('admin.users.edit', compact('user','title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditUserRequest $request, string $id)
    {
        $user = User::query()->find($id);
        User::updateUser($request,$user);
        return redirect()->route('users.index')->with('message', 'کاربر جدید با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function createUserRole($id)
    {
        $user = User::query()->find($id);
        $roles = Role::query()->get();
        return view('admin.users.user_roles',compact('user','roles'));
    }
    public function storeUserRole(Request $request,$id)
    {
        $user = User::query()->find($id);
        $user->syncRoles($request->roles);
        return redirect()->route('users.index')->with('message', 'کاربر باموفقیت به نقش متصل شد');
    }
}
