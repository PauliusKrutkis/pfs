<?php
/*
Plugin Name: PFS
*/

foreach (glob(plugin_dir_path(__FILE__) . "src/*.php") as $file) {
    include_once $file;
}

new \Pfs\Setup(ABSPATH . 'wp-content/plugins/pfs');