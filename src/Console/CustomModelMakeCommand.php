<?php

namespace Maxcelos\LaravelUtils\Console;

use Illuminate\Foundation\Console\ModelMakeCommand;

class CustomModelMakeCommand extends ModelMakeCommand
{
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . $this->strToNamespace(config('maxcelos.models_base_path'));
    }

    protected function strToNamespace($value = null)
    {
        $value = $value ? '\\' . ucwords(camel_case(str_replace('/', '\\', str_replace('/', '/ ', str_replace('\\', '\\ ', $value))))) : '';

        return str_replace('\\\\', '\\', $value);
    }
}
