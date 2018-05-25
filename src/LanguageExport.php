<?php

namespace Maxcelos\LaravelUtils;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;


class LanguageExport
{
    public static function all($lang)
    {
        $files = new Filesystem;

        $trans = [];

        foreach ($files->directories(app()->langPath()) as $langPath) {

            $locale = basename($langPath);

            if($lang == $locale){
                    foreach ($files->allfiles($langPath) as $file) {
                    $info = pathinfo($file);
                    $group = $info['filename'];

                    if (!in_array($group, ['validation'])) {
                        $subLangPath = str_replace($langPath . DIRECTORY_SEPARATOR, "", $info['dirname']);
                        $subLangPath = str_replace(DIRECTORY_SEPARATOR, "/", $subLangPath);

                        if ($subLangPath != $langPath) {
                            $group = $subLangPath . "/" . $group;
                        }

                        $translations = \Lang::getLoader()->load($locale, $group);

                        if ($translations && is_array($translations)) {
                            foreach (array_dot($translations) as $key => $value) {
                                $regex = '~(:\w+)~';
                                if (preg_match_all($regex, $value, $matches, PREG_PATTERN_ORDER)) {
                                    foreach ($matches[1] as $word) {
                                        $word = $word. '}';
                                        $value = str_replace(':', '{', $word);
                                    }
                                }
                                $trans[$group][$key] = $value;
                            }
                        }
                    }
                }
            }
        }

        return $trans;
    }
}
