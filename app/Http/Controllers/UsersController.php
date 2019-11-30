<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Mail;
class UsersController extends Controller
{
    /**
     * 我们提倡在控制器 Auth 中间件使用中，首选except 方法，这样的话，当你新增一个控制器方法时，默认是安全的，此为最佳实践
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store','index','confirmEmail'] ]); //除了用户展示、列表、注册不需要用户登录其它的都要经过中间件检测

        $this->middleware('guest', [
            'only' => ['create'] ]);
    }

    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }

    /**
     * 用户列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
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
     */
    public function show(User $user)
    {
        $statuses = $user->statuses()
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);

        return view('users.show', compact('user','statuses'));
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
        //Auth::login($user);
        /**
         * 由于 HTTP 协议是无状态的，所以 Laravel 提供了一种用于临时保存用户数据的方法 - （Session），
           并附带支持多种会话后端驱动，可通过统一的 API 进行使用
         */
        //session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        //return redirect()->route('users.show', [$user]);

        /**
         * 9.2章改用户注册后要进行邮箱验证
         */
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
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

    /**
     * 管理员删除普通用户操作
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    /**
     * 用户验证邮箱
     * @param $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();
        $user->activated = true;
        $user->activation_token = null;
        $user->save();
        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
    /**
     * 发送邮件给指定用户
     * @param $user
     */
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';           //发送邮件的视图
        $data = compact('user');   //发送的数据
        $from = 'summer@example.com';       //发送者邮箱
        $name = 'Summer';                   //发送者名称
        $to = $user->email;                 //发给谁
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
            /**
             * 由于使用真实的邮箱发送,就不需要使用from了
             */
            //$message->to($to)->subject($subject);
        });
    }
}
