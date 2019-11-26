<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    /**
     * 获得当前这条微博的发表者,一条微博属于一个用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
