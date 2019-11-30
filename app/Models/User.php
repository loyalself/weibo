<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * Notifiable是消息通知相关功能引用
     *
     * Authenticatable是授权相关功能的引用
     */
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * 第一版:该方法将当前用户发布过的所有微博从数据库中取出
     * 第二版改进:
     * 1. 通过 followings 方法取出所有关注用户的信息，再借助 pluck 方法将 id 进行分离并 赋值给 user_ids ；
     * 2. 将当前用户的 id 加入到 user_ids 数组中；
     * 3. 使用 Laravel 提供的 查询构造器 whereIn 方法取出所有用户的微博动态并进行倒序排序；
     * 4. 我们使用了 Eloquent 关联的预加载 with 方法，预加载避免了 N+1 查找的问题 ，大大提高了查询效率。
     *     N+1 问题 的例子可以阅读此文档Eloquent模型关系预加载。
     *
     * 这里需要注意的是 Auth::user()->followings 的用法。我们在 User 模型里定义了关联方法followings(),
     * 关联关系定义好后，我们就可以通过访问 followings 属性直接获取到关注用户的 集合。
     * 这是 Laravel Eloquent 提供的「动态属性」属性功能，我们可以像在访问模型中定义的属性一 样，来访问所有的关联方法。
     *
     * 还有一点需要注意的是 $user->followings 与 $user->followings()调用时返回的数据是不一 样的,
     * $user->followings 返回的是Eloquent集合;而$user->followings()返回的是数据库请求构造器.
     * followings()的情况下，你需要使用:$user->followings()->get()或$user->followings()->paginate()。
     * 方法才能获取到最终数据。可以简单理解为 followings 返回的是数据集合，而 followings()返回的是数据库查询语句。
     *  如果使用 get() 方法的话：
        $user->followings == $user->followings()->get() // 等于 true
     */
    public function feed()
    {
        //第一版:return $this->statuses()->orderBy('created_at', 'desc');

        $user_ids = $this->followings->pluck('id')->toArray();
        array_push($user_ids, $this->id);
        return Status::whereIn('user_id', $user_ids) ->with('user') ->orderBy('created_at', 'desc');
    }
    /**
     * 一个用户拥有多条微博
     */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }
    /**
     * 关注用户
     * @param $user_ids
     */
    public function follow($user_ids)
    {
        if ( ! is_array($user_ids))
        {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }
    /**
     * 取消关注
     * @param $user_ids
     */
    public function unfollow($user_ids)
    {
        if(!is_array($user_ids))
        {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    /**
     * 判断当前登录的用户 A 是否关注了用户 B,我们只需判断 用户 B 是否包含在用户 A 的关注人列表上即可
     * @param $user_id
     * @return mixed
     */
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
    /**
     * 多对多
     * 一个用户可以拥有多个粉丝。
     * 我们可以通过 followers 来获取粉丝关系列表，如：$user->followers();
     */
    public function followers()
    {
        return $this->belongsToMany(User::class,'followers','user_id', 'follower_id');
    }
    /**
     * 多对多
     * 一个用户可以关注很多个人。
     * 通过 followings 来获取用户关注人列表，如：$user->followings();
     */
    public function followings()
    {
        return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }

    /**
     * 生成用户头像方法
     * @param string $size
     * @return string
     */
    public function gravatar($size = '100')
    {
        /**
         * 通过 $this->attributes['email'] 获取到用户的邮箱
         * 将小写的邮箱使用 md5 方法进行转码；
         * 将转码后的邮箱与链接、尺寸拼接成完整的 URL 并返回；
         */
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    /**
     * boot方法会在用户模型类完成初始化之后进行加载，因此我们对事件的监听需要放在该方法中
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }
}
