<?php

namespace Icinga\Cli;

use Icinga\Cli\Loader;
use Icinga\Cli\Screen;
use Icinga\Application\ApplicationBootstrap as App;
use Exception;

abstract class Command
{
    protected $app;
    protected $docs;
    protected $params;
    protected $screen;
    protected $isVerbose;
    protected $isDebugging;

    protected $moduleName;
    protected $commandName;
    protected $actionName;
    
    protected $defaultActionName = 'default';

    public function __construct(App $app, $moduleName, $commandName, $actionName, $initialize = true)
    {
        $this->app = $app;
        $this->moduleName  = $moduleName;
        $this->commandName = $commandName;
        $this->actionName  = $actionName;
        $this->params  = $app->getParams();
        $this->screen  = Screen::instance($app);
        $this->isVerbose  = $this->params->shift('verbose', false);
        $this->isDebuging = $this->params->shift('debug', false);
        if ($initialize) {
            $this->init();
        }
    }

    public function hasRemainingParams()
    {
        return $this->params->count() > 0;
    }

    public function fail($msg)
    {
        throw new Exception($msg);
    }

    public function getDefaultActionName()
    {
        return $this->defaultActionName;
    }

    public function hasDefaultActionName()
    {
        return $this->hasActionName($this->defaultActionName);
    }

    public function hasActionName($name)
    {
        $actions = $this->listActions();
        return in_array($name, $actions);
    }

    public function listActions()
    {
        $actions = array();
        foreach (get_class_methods($this) as $method) {
            if (preg_match('~^([A-Za-z0-9]+)Action$~', $method, $m)) {
                $actions[] = $m[1];
            }
        }
        sort($actions);
        return $actions;
    }

    public function docs()
    {
        if ($this->docs === null) {
            $this->docs = new Documentation($this->app);
        }
        return $this->docs;
    }

    public function showUsage($action = null)
    {
        if ($action === null) {
            $action = $this->actionName;
        }
        echo $this->docs()->usage(
            $this->moduleName,
            $this->commandName,
            $action
        );
    }

    public function init()
    {
    }
}
