<?php
/**
 * HarvestHQ Chosen plugin bootstrap
 */

// Settings

/**
 * Webroot-friendly folder name. MyPlugin -> /my_plugin
 * @link http://book.cakephp.org/2.0/en/plugins.html#plugin-assets
 */
Configure::write('Chosen', array(
    'webroot' => Inflector::underscore(basename(dirname(__DIR__))),
));
