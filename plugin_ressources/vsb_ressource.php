<?php
/**
 * Plugin Name: VSB Ressource
 * Description: Gestion des ressources d'entraînement (exercices, tactiques, stratégies) pour les coachs.
 * Version: 1.0
 * Author: Team VSB
 */

if (!defined('ABSPATH')) exit;

// Autoload
spl_autoload_register(function ($class) {
    if (strpos($class, 'VSB\\') !== 0) return;
    $path = plugin_dir_path(__FILE__) . 'includes/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (file_exists($path)) require_once $path;
});

// Créer le dossier d’upload s’il n’existe pas
register_activation_hook(__FILE__, function() {
    $upload_dir = plugin_dir_path(__FILE__) . 'uploads';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0755, true);
});

// Register shortcode
add_action('init', function () {
    (new \VSB\controllers\RessourceController())->registerShortcodes();

});
