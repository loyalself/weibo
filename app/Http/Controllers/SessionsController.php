<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class SessionsController extends Controller
{
    /**
     * Auth 中间件提供的 guest 选项，用于指定一些只允许未登录用户访问的动作
     */
    public function __construct()
    {
        //只允许未登录用户才能访问登录页面
        $this->middleware('guest', ['only' => ['create'] ]);
    }

    /**
     * 登录页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('sessions.create');
    }

    /**
     * 登录逻辑
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
        /**
         *  Laravel 提供的 Auth::user() 方法来获取当前登录用户的信息
         *  Auth::attempt() 方法可接收两个参数，第一个参数为需要进行用户身份认证的数 组，第二个参数为是否为用户开启『记住我』功能的布尔值。
         */
        if (Auth::attempt($credentials,$request->has('remember')))
        {
            if(Auth::user()->activated)
            {
                session()->flash('success', '欢迎回来！');
                /**
                 *  redirect() 实例提供了一个 intended 方法，
                 * 该方法可将页面重定向到上一次请求尝试访问的页面上，并接收一个默认跳转地址参数，当上一次请求记录 为空时，跳转到默认地址上
                 */
                $fallback = route('users.show', Auth::user());
                return redirect()->intended($fallback);
            }else{
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
            //return redirect()->route('users.show', [Auth::user()]);
        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }
    }

    /**
     * 退出
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}
