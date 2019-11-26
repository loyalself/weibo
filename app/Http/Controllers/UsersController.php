<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;
class UsersController extends Controller
{
    /**
     * 我们提倡在控制器 Auth 中间件使用中，首选except 方法，这样的话，当你新增一个控制器方法时，默认是安全的，此为最佳实践
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store'] ]); //除了用户展示、注册不需要用户登录其它的都要经过中间件检测

        $this->middleware('guest', [
            'only' => ['create'] ]);
    }

    /**
     * 用户注册页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * 用户信息展示页面
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
    /**
     * 用户注册逻辑
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
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
         * 用户注册后自动登录
         */
        Auth::login($user);

        /**
         * 由于 HTTP 协议是无状态的，所以 Laravel 提供了一种用于临时保存用户数据的方法 - （Session），
           并附带支持多种会话后端驱动，可通过统一的 API 进行使用
         */
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }
    /**
     * 用户编辑页面
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }
    /**
     * 用户编辑逻辑
     */
    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        $this->validate($request, [
            'name'      => 'required|max:50',
            'password'  => 'nullable|confirmed|min:6'   //有的人不想修改密码,密码允许为空
        ]);
        $data = [];
        $data['name'] = $request->name;
        if($request->password)
        {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success', '个人资料更新成功！');
        return redirect()->route('users.show', $user);
    }
}
