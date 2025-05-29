<?php

add_shortcode('ajouter_otr', 'ajout_otr_form');

function ajout_otr_form(){
    include MY_PLUGIN_PATH . '/includes/templates/ajouter-otr-form.php';
}

