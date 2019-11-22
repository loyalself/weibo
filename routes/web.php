<?php
/**
 * 路由后面加个name的意思:对应html页面,比如:name('abc') ,则在html页面链接应该href='abc'这样写找的到
 */

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');
