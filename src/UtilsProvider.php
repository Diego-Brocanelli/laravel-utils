<?php

namespace Maxcelos\LaravelUtils;

use Illuminate\Console\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Maxcelos\LaravelUtils\Console\CustomModelMakeCommand;
use Maxcelos\LaravelUtils\Console\CommandsList;

class UtilsProvider extends ServiceProvider
{
    /**
     * Path to configuration file.
     *
     * @return string
     */
    public function configPath(): string
    {
        return realpath(__DIR__ . '/config/maxcelos.php');
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->publishes([$this->configPath() => config_path('maxcelos.php')], 'config');

        // Custom collection pagination
        Collection::macro('paginate', function ($request){
            if(isset($request->dt) && $request->dt){
                $request['limit'] = $request->length;
                $request['page'] = ($request->start / $request->length) + 1;
                $column = $request->order[0]['column'];
                $order = $request->order[0]['dir'] == 'asc' ? 'sort_by' : 'sort_by_desc';
                $request[$order] = $request->columns[$column]['data'];
            }

            $limit = $request->limit ?: 10;
            $page = $request->page ?: 1;
            $offSet = ($page * $limit) - $limit;

            // Sort by asc or desc order
            if (isset($request->sort_by)) {
                $collection = $this->sortBy($request->sort_by, SORT_NATURAL|SORT_FLAG_CASE);
            } elseif (isset($request->sort_by_desc)) {
                $collection = $this->sortByDesc($request->sort_by_desc, SORT_NATURAL|SORT_FLAG_CASE);
            } else {
                $collection = $this;
            }

            // Isolate current page items
            $itemsForCurrentPage = $collection->slice($offSet, $limit);

            // Prepare pagination structure
            $paginated = new LengthAwarePaginator($itemsForCurrentPage->values(), $collection->count(), $limit, $page);

            // Prevent over paging
            if($paginated->lastPage() < $page){
                $request->page = $paginated->lastPage();
                $paginated = $collection->paginate($request);
            }

            return $paginated;
        });
    }

    public function register()
    {
        //dd($this->configPath());
        
        $this->mergeConfigFrom($this->configPath(), 'maxcelos');
        $this->app->extend('command.model.make', function (){
            return new CustomModelMakeCommand($this->app->files);
        });
    }
}
