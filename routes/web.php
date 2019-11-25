<?php
/**
 * 路由后面加个name的意思:对应html页面,比如:name('abc') ,则在html页面链接应该href='abc'这样写找的到
 */

use Illuminate\Support\Facades\Route;

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

Route::get('signup', 'UsersController@create')->name('signup');

/**
 *  resource 方法将遵从 RESTful 架构为用户资源生成路由。该方法接收两个参数，第一个参数 为资源名称，第二个参数为控制器名称
 */
Route::resource('users', 'UsersController');
//Route::get('/users', 'UsersController@index')->name('users.index');                显示所有用户列表的页面
//Route::get('/users/{user}', 'UsersController@show')->name('users.show');           显示用户个人信息的页面
//Route::get('/users/create', 'UsersController@create')->name('users.create');       创建用户的页面
//Route::post('/users', 'UsersController@store')->name('users.store');               创建用户
//Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');      编辑用户个人资料的页面
//Route::patch('/users/{user}', 'UsersController@update')->name('users.update');     更新用户
//Route::delete('/users/{user}', 'UsersController@destroy')->name('users.destroy');  删除用户
