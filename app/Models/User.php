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
     * 该方法将当前用户发布过的所有微博从数据库中取出
     */
    public function feed()
    {
        return $this->statuses()->orderBy('created_at', 'desc');
    }
    /**
     * 一个用户拥有多条微博
     */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function follow($user_ids)
    {
        if ( ! is_array($user_ids))
        {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

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
