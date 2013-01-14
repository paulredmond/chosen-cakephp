<?php

namespace Chosen;

class Installer
{
    public static function postInstall()
    {
        $target = __DIR__ . '/../../../../Vendor/harvesthq/chosen/chosen';
        $link = realpath(__DIR__ . '/../../webroot/chosen');
        symlink($target, $link);
    }
}