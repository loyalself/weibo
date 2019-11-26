<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['content'];

    /**
     * 获得当前这条微博的发表者,一条微博属于一个用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * 如果没有一对多的关系，我们需要这样来创建一条微博。App\Models\Status::create()
     *
     * 当我们将用户模型与微博模型进行一对多关联之后，我们得到了以下方法。
     * $user->statuses()->create()
     * 这样在微博进行创建时便会自动关联与微博用户之间的关系，非常方便
     */
}
