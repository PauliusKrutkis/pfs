<?php
/**
 * Plugin Name: PFS
 * Description: Simple wordpress post filter system
 * Version: 1.0.0
 * Author: Paulius Krutkis
 * Author URI: https://github.com/PauliusKrutkis
 * License: GPLv2
 */

foreach (glob(plugin_dir_path(__FILE__) . "src/*.php") as $file) {
    include_once $file;
}

new \Pfs\Setup(ABSPATH . 'wp-content/plugins/pfs');