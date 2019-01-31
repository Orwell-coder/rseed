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
     * DatabaseSeeder file save floder
     *
     * @var [type]
     */
    private $seederPath = null;


  
    /**
     * build a seeder file
     *
     * @param [type] $data
     * @param [type] $fileName
     * @param [type] $prefix
     * @param [type] $suffix
     * @param integer $max
     * @param integer $chunkSize
     * @param [type] $prerunEvent
     * @param [type] $postrunEvent
     * @param boolean $indexed
     * @return void
     */
    public function buildSeed($data, $fileName, $prefix=null, $suffix=null,  $max = 0, $chunkSize = 0, $prerunEvent = null, $postrunEvent = null,  $indexed = true)
    {
        $chunkSize  = $chunkSize > 0 ? $chunkSize : 500;
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
            $fileName,
            $dataArray,
            $chunkSize,
            $prerunEvent,
            $postrunEvent,
            $indexed
        );
        // Save a populated stub
        $this->files->put($seedsPath, $seedContent);
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
        $this->setDatabaseSeederPath();
        return $this;
    }

    /**
     * Get DatabaseSeeder file name
     *
     * @return void
     */
    public function getDatabaseSeederPath()
    {
        return $this->seederPath ?? base_path() . config('iseed::config.path') . '/DatabaseSeeder.php';
    }

    /**
     * Undocumented function
     *
     * @param [type] $path
     * @return void
     */
    public function setDatabaseSeederPath()
    {
        try{
            if(!$this->path){
                throw new \Exception("Please set SeedPath floder !");
            }
            copy(__DIR__ . DIRECTORY_SEPARATOR . 'template'. DIRECTORY_SEPARATOR . 'DatabaseSeeder.php', $this->path);
            $this->seederPath = $this->path;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
        
    }

    /**
     * Cleans the iSeed section
     * @return bool
     */
    public function cleanSection()
    {
        $databaseSeederPath = $this->getDatabaseSeederPath();

        $content = $this->files->get($databaseSeederPath);

        $content = preg_replace("/(\#iseed_start.+?)\#iseed_end/us", "#iseed_start\n\t\t#iseed_end", $content);

        return $this->files->put($databaseSeederPath, $content) !== false;
        return false;
    }

    /**
     * Updates the DatabaseSeeder file's run method (kudoz to: https://github.com/JeffreyWay/Laravel-4-Generators)
     * @param  string  $className
     * @return bool
     */
    public function updateDatabaseSeederRunMethod($className)
    {
        $databaseSeederPath = $this->getDatabaseSeederPath();

        $content = $this->files->get($databaseSeederPath);
        if (strpos($content, "\$this->call({$className}::class)") === false) {
            if (
                strpos($content, '#iseed_start') &&
                strpos($content, '#iseed_end') &&
                strpos($content, '#iseed_start') < strpos($content, '#iseed_end')
            ) {
                $content = preg_replace("/(\#iseed_start.+?)(\#iseed_end)/us", "$1\$this->call({$className}::class);{$this->newLineCharacter}{$this->indentCharacter}{$this->indentCharacter}$2", $content);
            } else {
                $content = preg_replace("/(run\(\).+?)}/us", "$1{$this->indentCharacter}\$this->call({$className}::class);{$this->newLineCharacter}{$this->indentCharacter}}", $content);
            }
        }

        return $this->files->put($databaseSeederPath, $content) !== false;
    }

   



 

}