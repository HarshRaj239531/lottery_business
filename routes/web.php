<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin-panel');
});

Route::get('/admin-panel', function () {
    return view('admin.dashboard');
});



