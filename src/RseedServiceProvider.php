<?php

namespace Jsonphper\Rseed;

use Illuminate\Support\ServiceProvider;

class RseedServiceProvider extends ServiceProvider
{

    protected $packagePath = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;

    protected $fileName = 'rseed.php';

    /**
     * Undocumented function
     *
     * @return void
     */
    public function boot()
    {
        $confName = $this->packagePath . 'config' . DIRECTORY_SEPARATOR . $this->fileName;
        $this->publishes([
            $confName => config_path()
        ]);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function register()
    {

    }
}