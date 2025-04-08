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
    }

    public function initialize(){
        include_once(MY_PLUGIN_PATH . 'includes/utilities.php');
        include_once(MY_PLUGIN_PATH . 'includes/option-page.php');
        include_once(MY_PLUGIN_PATH . 'includes/ajouter-joueur.php');
        include_once(MY_PLUGIN_PATH . 'includes/liste-joueurs.php');
        include_once(MY_PLUGIN_PATH . 'includes/liste-equipes.php');
        include_once(MY_PLUGIN_PATH . 'includes/api.php');
        include_once(MY_PLUGIN_PATH . 'includes/modifier-joueur.php');

    }

 }
 $gestionEquipePlugin = new GestionEquipe();
 $gestionEquipePlugin->initialize();
}