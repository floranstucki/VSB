<?php

add_shortcode('ajouter_personne', 'ajout_personne_form');

function ajout_personne_form(){
    include MY_PLUGIN_PATH . '/includes/templates/ajouter-personne-form.php';
}

