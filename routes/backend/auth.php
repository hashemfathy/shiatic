<?php

Route::post('login', 'AuthController@login')->name('login');
Route::get('', 'AuthController@index')->name('index');
Route::get('logout', 'AuthController@logout')->name('logout');
