<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param User $currentUser  当前登录用户实例
     * @param User $user         要进行授权的用户实例
     *
     * 当两个 id 相同时，则代表两个用户是相同用户，用户通过授权，可以接着进行下一个操作。如果 id不相同的话，将抛出 403 异常信息来拒绝访问
     */
    public function update(User $currentUser, User $user)
    {

    }


    /**
     * 使用授权策略需要注意以下两点：
     * 1. 我们并不需要检查 $currentUser 是不是 NULL。未登录用户，框架会自动为其 所有权限 返回false ；
     * 2. 调用时，默认情况下，我们 不需要 传递当前登录用户至该方法内，因为框架会自动加载当前登录 用户。
     *
     * 接下来我们还需要在 AuthServiceProvider 类中对授权策略进行设置。
     * AuthServiceProvider包含了一个 policies 属性，该属性用于将各种模型对应到管理它们的授权策略上。
     * 我们需要为用户 模型 User 指定授权策略 UserPolicy。
     */
}
