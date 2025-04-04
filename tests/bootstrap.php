<?php
/*
// Définir les chemins de base
$_tests_dir = getenv('WP_TESTS_DIR') ?: '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

// Charger le plugin
function _manually_load_plugin() {
    require dirname(__DIR__) . '/plugin_gestion_vsb.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Démarrer WordPress
require $_tests_dir . '/includes/bootstrap.php';
*/

require_once __DIR__ . '/../vendor/autoload.php';
