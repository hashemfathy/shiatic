<?php

Route::group(['middleware' => 'auth'], function () {
    Route::get('posts/all', 'PostController@all');
});
