<?php

use Maxcelos\LaravelUtils\LanguageExport;

Route::get(config('maxcelos.trans_route'), function ($lang) {
    return LanguageExport::all($lang);
})->middleware('auth:api');
