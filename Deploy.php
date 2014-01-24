<?php

abstract class Deploy
{
    const BASEDIR = '/var/www/html/';

    protected $branchName;
    protected $payload;

    function __construct($payload) {
        $this->payload = $payload;
        $this->branchName = str_replace('_', '-', basename($payload->ref));
    }

    public abstract function run(Shell $shell);
}