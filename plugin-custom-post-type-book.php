<?php

/**
 * Plugin Name: Plugin Custom Post Type Book
 * Plugin URI: https://andrevega.com
 * Description: Plugin to create a CPT Book for the company Global News Canada
 * Version: 0.1
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Andrés Vega
 * Author URI: https://andrevega.com
 * License: GPL v2 or later
 * Text Domain: cpt-book
 * Domain Path: /languages
 */

define('CPT_BOOK', plugin_dir_path((__FILE__)));
require_once CPT_BOOK . '/admin/admin-index.php';
