<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required|max:50',
            'email'     => 'required|email|unique:users|max:255',
            'password'  => 'required|confirmed|min:6'
        ]);
        //用户模型 User::create() 创建成功后会返回一个用户对象，并包含新注册用户的所有信息。
        $user = User::create(
            [
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password)
            ]);
        /**
         * 由于 HTTP 协议是无状态的，所以 Laravel 提供了一种用于临时保存用户数据的方法 - （Session），
           并附带支持多种会话后端驱动，可通过统一的 API 进行使用
         */
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }
}
