<?php

namespace Deploy;

use Deploy;
use Shell;

class WebProd extends Deploy
{
    const DIR = 'tweetWeb/';
    const REPO = 'tweetWeb';

    public function run(Shell $shell) {
        $branchDir = self::BASEDIR . static::DIR . $this->branchName;
        if (!file_exists($branchDir)) {
            return false;
        }
        $symfonyConsoleOptions = '--env=prod --no-debug';

        return $shell->changeDir($branchDir)
            ->andGitPull()
            ->andComposerInstall()
            ->andAssetsInstall($symfonyConsoleOptions)
            ->andAsseticDump($symfonyConsoleOptions)
            ->andCacheClear($symfonyConsoleOptions)
            ->exec();
    }
}