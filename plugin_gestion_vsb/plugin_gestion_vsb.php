<?php
/**
 * Plugin Name: Gestion VSB
 * Description: Plugin de gestion du club Veyrier Salève Basket.
 * Version: 1.0
 * Author: Team VSB
 */

 use \VSB\controllers\MatchController;
 use \VSB\controllers\StatsController;
 use \VSB\controllers\OTRController;
 use \VSB\controllers\ComptaController;
 use \VSB\controllers\CalendrierController;
if (!defined('ABSPATH')) exit;

// Autoloader simple basé sur les namespaces
spl_autoload_register(function ($class) {
    if (strpos($class, 'VSB\\') !== 0) return;

    $path = plugin_dir_path(__FILE__) . 'includes/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (file_exists($path)) require_once $path;
});

// Initialisation des shortcodes
add_action('init', 'plugin_gestion_vsb_init');

function plugin_gestion_vsb_init() {
    (new MatchController())->registerShortcodes();
    (new StatsController())->registerShortcodes();
    (new OTRController())->registerShortcodes();
    (new ComptaController())->registerShortcodes();
    (new CalendrierController())->registerShortcodes();
}
