<?php
/**
 * 路由后面加个name的意思:对应html页面,比如:name('abc') ,则在html页面链接应该href='abc'这样写找的到
 */

use Illuminate\Support\Facades\Route;

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

Route::get('signup', 'UsersController@create')->name('signup');
Route::get('signup/confirm/{token}','UsersController@confirmEmail')->name('confirm_email'); //激活邮箱

Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request'); //显示重置密码的邮箱发送页面
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');   //邮箱发送重设链接
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');  //密码更新页面
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');                //执行密码更新操作

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


Route::get('login', 'SessionsController@create')->name('login');         //显示登录页面
Route::post('login', 'SessionsController@store')->name('login');         //创建新会话（登录）
Route::delete('logout', 'SessionsController@destroy')->name('logout');   //销毁会话（退出登录）

/**
 * 只处理创建和删除微博的需求
 */
Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);


Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');  //显示用户的关注人列表
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');     //显示用户的粉丝列表

Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');           //用户关注操作
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');     //用户取关操作
