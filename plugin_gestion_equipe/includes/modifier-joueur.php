<?php

add_shortcode('modifier', 'modifier_joueur_form');

function modifier_joueur_form()
{
    include MY_PLUGIN_PATH . '/includes/templates/modifier-joueur-form.php';
}
