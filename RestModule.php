<?php
namespace Grout\Cyantree\RestModule;

use Cyantree\Grout\App\Module;
use Grout\Cyantree\RestModule\Types\RestConfig;
use Cyantree\Grout\App\Plugin;

class RestModule extends Module
{
    /** @var RestConfig */
    public $moduleConfig;

    /** @var Plugin[] */
    public $plugin = array();

    public function init()
    {
        $this->app->configs->setDefaultConfig($this->id, new RestConfig());
        $this->moduleConfig = $this->app->configs->getConfig($this->id);

        foreach ($this->moduleConfig->plugins as $plugin) {
            $this->importPlugin($plugin['plugin'], $plugin);
        }
    }

    public function initTask($task)
    {
        foreach($this->plugins as $plugin){
            $plugin->initTask($task);
        }
    }


    public function beforeParsing($task)
    {
        if($task->plugin){
            $task->plugin->beforeParsing($task);
        }
    }

    public function afterParsing($task)
    {
        if($task->plugin){
            $task->plugin->afterParsing($task);
        }
    }
}