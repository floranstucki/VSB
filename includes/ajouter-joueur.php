<?php

add_shortcode('ajouter', 'ajout_joueur_form');
function ajout_joueur_form(){
    include MY_PLUGIN_PATH . '/includes/templates/ajout-joueur-form.php';
}

