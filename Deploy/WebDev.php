<?php

namespace Deploy;

use Deploy;
use Shell;

class WebDev extends Deploy
{
    const DIR = 'tweetWeb/';
    const REPO = 'tweetWeb';

    public function run(Shell $shell) {
        $branchDir = self::BASEDIR . static::DIR . $this->branchName;
        if (!file_exists($branchDir)) {
            return false;
        }
        $shell->changeDir($branchDir)
            ->andGitPull()
            ->andComposerInstall();

        if (self::hasAddedFiles($this->payload)) {
            $shell->andAssetsInstall();
        }

        return $shell->andAsseticDump()
            ->andCacheClear()
            ->exec();
    }

    private static function hasAddedFiles($payload) {
        foreach ($payload->commits as $commit) {
            if (!empty($commit->added)) {
                return true;
            }
        }
        return false;
    }
}