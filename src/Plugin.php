<?php

namespace Fsylum\EmailTools;

use Fsylum\EmailTools\Contracts\Service;

class Plugin
{
    const CAPABILITY = 'manage_options';
    const SLUG       = 'fs-email-tools';

    protected $services = [];

    public function addService(Service $service)
    {
        $this->services[] = $service;
    }

    public function run()
    {
        foreach ($this->services as $service) {
            (new $service)->run();
        }
    }
}
