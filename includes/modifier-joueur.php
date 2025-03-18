<?php

add_shortcode('modifier', 'modifier_joueur_form');
add_action('rest_api_init', 'update_rest_endpoint');

function modifier_joueur_form(){
    include MY_PLUGIN_PATH . '/includes/templates/modifier-joueur-form.php';
}

function update_rest_endpoint(){
    error_log('Api rest enregistrée');
    register_rest_route('v1', '/modifier-joueur-form/submit', array(
        'methods' => 'POST',
        'callback' => 'modifier_joueur',
        'permission_callback' => '__return_true'
    )); 
}

function modifier_joueur($request){
    $params = $request->get_params();

    $index = (int) $params['index'];
    $file_path = plugin_dir_path(__FILE__) . 'joueur_form.json';

    if (!file_exists($file_path)) {
        return new WP_REST_Response(['message' => 'Fichier introuvable'], 404);
    }

    $json_data = file_get_contents($file_path);
    $joueurs = json_decode($json_data, true);

    if (!isset($joueurs[$index])) {
        return new WP_REST_Response(['message' => 'Joueur introuvable'], 404);
    }

    // Mise à jour des informations du joueur
    $joueurs[$index] = [
        'name' => sanitize_text_field($params['name']),
        'firstName' => sanitize_text_field($params['firstName']),
        'dateNaissance' => sanitize_text_field($params['dateNaissance']),
        'address' => sanitize_text_field($params['address']),
        'npa' => sanitize_text_field($params['npa']),
        'equipe' => sanitize_text_field($params['equipe']),
    ];

    // Sauvegarde du fichier JSON
    file_put_contents($file_path, json_encode($joueurs, JSON_PRETTY_PRINT));

    return new WP_REST_Response(['message' => 'Joueur modifié avec succès'], 200);
}

