<?php


Route::namespace("Tolacika\CronBundle\Http\Controllers")
    ->prefix("CronBundle")
    ->name("cron-bundle.")
     ->group(function () {
         Route::get('/', 'CronBundleController@index')->name('index');
         Route::get('/{job}/show', 'CronBundleController@show')->name('show');
         Route::get('/create', 'CronBundleController@create')->name('create');
         Route::post('/store', 'CronBundleController@store')->name('store');
         Route::get('/{job}/edit', 'CronBundleController@edit')->name('edit');
         Route::post('/{job}/update', 'CronBundleController@update')->name('update');
         Route::get('/{job}/destroy', 'CronBundleController@destroy')->name('destroy');
         Route::post('/{job}/destroy', 'CronBundleController@destroy')->name('destroy');

         Route::get('/assets/js/{file}', 'CronBundleController@assetjs')
              ->name('assets.js');
         Route::get('/assets/css/{file}', 'CronBundleController@assetcss')
              ->name('assets.css');
     });
