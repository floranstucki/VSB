<?php

/**
 * Plugin Name: Gestion d'équipes
 * Description: Gestion des membres des équipes d'une association sportive
 * Version: 1.0
 * Author: VSB  
 */

 if(!defined('ABSPATH')) {
     die('you cannot be here');
 }

 if(!class_exists('GestionEquipe')){
 class GestionEquipe{
    public function __construct(){
        define('MY_PLUGIN_PATH',plugin_dir_path(__FILE__));
        require_once(MY_PLUGIN_PATH . '/vendor/autoload.php');
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function initialize(){
        include_once(MY_PLUGIN_PATH . 'includes/ajouter-joueur.php');
        include_once(MY_PLUGIN_PATH . 'includes/ajouter-otr.php');
        include_once(MY_PLUGIN_PATH . 'includes/ajouter-personne.php');
        include_once(MY_PLUGIN_PATH . 'includes/liste-joueurs.php');
        include_once(MY_PLUGIN_PATH . 'includes/liste-equipes.php');
        include_once(MY_PLUGIN_PATH . 'includes/liste-personnes.php');
        include_once(MY_PLUGIN_PATH . 'includes/liste-otrs.php');
        include_once(MY_PLUGIN_PATH . 'includes/api.php');
        include_once(MY_PLUGIN_PATH . 'includes/modifier-joueur.php');
        include_once(MY_PLUGIN_PATH . 'includes/modifier-otr.php');
        include_once(MY_PLUGIN_PATH . 'includes/modifier-personne.php');

    }

    public function enqueue_styles() {
    wp_enqueue_style(
        'gestion-equipe-formulaires',
        plugin_dir_url(__FILE__) . 'assets/css/formulaires.css',
        array(),
        '1.0'
    );
}


 }
 $gestionEquipePlugin = new GestionEquipe();
 $gestionEquipePlugin->initialize();
}