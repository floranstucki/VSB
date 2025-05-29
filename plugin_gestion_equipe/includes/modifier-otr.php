<?php

add_shortcode('modifier_otr', 'modifier_otr_form');

function modifier_otr_form()
{
    include MY_PLUGIN_PATH . '/includes/templates/modifier-otr-form.php';
}
