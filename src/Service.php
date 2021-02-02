<?php

namespace Fsylum\EmailTools;

abstract class Service
{
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    abstract public function run();
}
