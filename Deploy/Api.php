<?php
namespace Deploy;

use Deploy;
use Shell;

class Api extends Deploy
{
    const DIR = 'tweetApi/';
    const REPO = 'tweetApi';

    public function run(Shell $shell) {
        $branchDir = self::BASEDIR . static::DIR . $this->branchName;
        if (!file_exists($branchDir)) {
            return false;
        }
        return $shell->changeDir($branchDir)
            ->andGitPull()
            ->andComposerInstall()
            ->exec();
    }
}