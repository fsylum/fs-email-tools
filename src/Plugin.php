<?php

namespace Fsylum\EmailTools;

use ReflectionClass;
use InvalidArgumentException;
use Fsylum\EmailTools\Service;

class Plugin
{
    const CAPABILITY = 'manage_options';
    const SLUG       = 'fs-email-tools';

    protected $services = [];

    public function addService($service)
    {
        if (!(new ReflectionClass($service))->isSubclassOf(Service::class)) {
            throw new InvalidArgumentException;
        }

        $this->services[] = $service;
    }

    public function run()
    {
        foreach ($this->services as $service) {
            (new $service($this))->run();
        }
    }
}
