<?php

add_shortcode('modifier_personne', 'modifier_personne_form');

function modifier_personne_form()
{
    include MY_PLUGIN_PATH . '/includes/templates/modifier-personne-form.php';
}
