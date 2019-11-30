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
     * 自己不能关注自己
     * @param User $currentUser
     * @param User $user
     * @return bool
     */
    public function follow(User $currentUser, User $user)
    {
        return $currentUser->id !== $user->id;
    }

    /**
     * @param User $currentUser  当前登录用户实例
     * @param User $user         要进行授权的用户实例
     *
     * 当两个 id 相同时，则代表两个用户是相同用户，用户通过授权，可以接着进行下一个操作。如果 id不相同的话，将抛出 403 异常信息来拒绝访问
     */
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    /**
     * 删除用户的动作，有两个逻辑需要提前考虑:
     * 1.只有当前登录用户为管理员才能执行删除操作；
     * 2. 删除的用户对象不是自己（即使是管理员也不能自己删自己）。
     */
    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }

    /**
     * 使用授权策略(讲解)需要注意以下两点：
     * 1. 我们并不需要检查 $currentUser 是不是 NULL。未登录用户，框架会自动为其 所有权限 返回false ；
     * 2. 调用时，默认情况下，我们 不需要 传递当前登录用户至该方法内，因为框架会自动加载当前登录 用户。
     *
     * 接下来我们还需要在 AuthServiceProvider 类中对授权策略进行设置。
     * AuthServiceProvider包含了一个 policies 属性，该属性用于将各种模型对应到管理它们的授权策略上。
     * 我们需要为用户 模型 User 指定授权策略 UserPolicy。
     */
}
