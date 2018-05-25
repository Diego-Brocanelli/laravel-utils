<?php

use Maxcelos\LaravelUtils\LanguageExport;

Route::get('api/language/{lang}', function ($lang) {
    return LanguageExport::all($lang);
})->middleware('auth:api');
