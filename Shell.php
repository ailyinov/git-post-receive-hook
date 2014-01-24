<?php

class Shell
{
    private $commands;
    private $baseDir;

    public function __construct($baseDir) {
        $this->commands = [];
        $this->baseDir = rtrim($baseDir, '/') . '/';
    }

    public function getCommands() {
        return $this->commands;
    }

    public function getBaseDir() {
        return $this->baseDir;
    }

    public function changeDir($dir) {
        $this->commands[] = "cd $dir";
        return $this;
    }

    public function gitPull() {
        $this->commands[] = 'git pull 2>&1';
        return $this;
    }

    public function cacheClear($options = '') {
        $this->commands[] = "app/console cache:clear $options";
        return $this;
    }

    public function asseticDump($options = '') {
        $this->commands[] = "app/console assetic:dump $options";
        return $this;
    }

    public function assetsInstall($options = '') {
        $this->commands[] = "app/console assets:install $options";
        return $this;
    }

    public function composerInstall() {
        $this->commands[] = "{$this->baseDir}tweetWeb/develop/composer.phar install 2>&1";
        return $this;
    }

    function __call($name, $args) {

        if (self::hasAndPrefix($name)) {
            $methodName = substr($name, 3);
            if (!method_exists($this, $methodName)) {
                throw new BadFunctionCallException("Can't call method $name");
            }
            $this->addAnd();
            return call_user_func_array([$this, $methodName], $args);
        }

        return false;
    }

    private static function hasAndPrefix($methodName) {
        return substr($methodName, 0, 3) == 'and';
    }

    private function addAnd() {
        $this->commands[] = '&&';
        return $this;
    }

    public function exec() {
        $this->commands[] = '2>&1';
        exec(join(' ', $this->commands), $out);
        return $out;
    }
} 