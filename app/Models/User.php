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
}