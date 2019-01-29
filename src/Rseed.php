<?php

namespace Jsonphper\Rseed;

use Orangehill\Iseed\Iseed;

class Rseed extends Iseed
{
    /**
     * Name of the database upon which the seed will be executed.
     *
     * @var string
     */
    protected $databaseName;
    /**
     * New line character for seed files.
     * Double quotes are mandatory!
     *
     * @var string
     */
    private $newLineCharacter = PHP_EOL;
    /**
     * Desired indent for the code.
     * For tabulator use \t
     * Double quotes are mandatory!
     *
     * @var string
     */
    private $indentCharacter = "    ";


    /**
     * File save directory
     *
     * @var [type]
     */
    private $path = null;


    /**
     * Generates a seed file.
     * @param  string   $table
     * @param  string   $prefix
     * @param  string   $suffix
     * @param  string   $database
     * @param  int      $max
     * @param  string   $prerunEvent
     * @param  string   $postunEvent
     * @return bool
     * @throws Orangehill\Iseed\TableNotFoundException
     */
    public function generateSeed($data, $fileName, $prefix=null, $suffix=null,  $max = 0, $chunkSize = 0, $prerunEvent = null, $postrunEvent = null, $dumpAuto = true, $indexed = true)
    {
        // Repack the data
        $dataArray = $this->repackSeedData($data);
        // Generate class name
        $className = $this->generateClassName($fileName, $prefix, $suffix);
        // Get template for a seed file contents
        $stub = $this->readStubFile($this->getStubPath() . '/seed.stub');
        // Get a seed folder path
        $seedPath = $this->getSeedPath();
        // Get a app/database/seeds path
        $seedsPath = $this->getPath($className, $seedPath);
        // Get a populated stub file
        $seedContent = $this->populateStub(
            $className,
            $stub,
            $table,
            $dataArray,
            $chunkSize,
            $prerunEvent,
            $postrunEvent,
            $indexed
        );
        // Save a populated stub
        $this->files->put($seedsPath, $seedContent);
        // Run composer dump-auto
        if ($dumpAuto) {
            $this->composer->dumpAutoloads();
        }
        // Update the DatabaseSeeder.php file
        return $this->updateDatabaseSeederRunMethod($className) !== false;
    }
    /**
     * Get a seed folder path
     * @return string
     */
    public function getSeedPath()
    {
        return $this->path ?? base_path() . config('iseed::config.path');
    }
    
    /**
     * Set a seed folder path
     *
     * @param [type] $path
     * @return void
     */
    public function setSeedPath($path)
    {
        $this->path = $path;
        return $this;
    }

   



 

}