<?php

namespace Chosen;

class Installer
{
    public static function postInstall()
    {
        $target = APP . '/Vendor/harvesthq/chosen/chosen';
        $link = APP . '/Plugin/Chosen/webroot/chosen';
        symlink($target, $link);
    }
}