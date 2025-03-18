<?php

add_shortcode('ajouter', 'ajout_joueur_form');
add_action('rest_api_init', 'create_rest_endpoint');
function ajout_joueur_form(){
    include MY_PLUGIN_PATH . '/includes/templates/ajout-joueur-form.php';
}

function create_rest_endpoint(){
    register_rest_route('v1/ajout-joueur-form','submit',array(
        'methods' => 'POST',
        'callback' => 'ajouter_joueur'
    ));

}

function ajouter_joueur($data) {
    $params = $data->get_params();

    // Vérification du nonce pour la sécurité
    if (!wp_verify_nonce($params['_wpnonce'], 'wp_rest')) {
        return new WP_REST_Response(['error' => 'Nonce invalide'], 422);
    }

    // Suppression des paramètres inutiles
    unset($params['_wpnonce']);
    unset($params['_wp_http_referer']);

    // Définition du chemin du fichier JSON
    $file_path = plugin_dir_path(__FILE__) . 'joueur_form.json';

    // Lire les joueurs existants
    if (file_exists($file_path)) {
        $json_content = file_get_contents($file_path);
        $existing_data = json_decode($json_content, true);

        // Si le JSON est mal formé ou vide, on initialise un tableau vide
        if (!is_array($existing_data)) {
            $existing_data = [];
        }
    } else {
        $existing_data = [];
    }

    // Ajouter le nouveau joueur
    $nouveau_joueur = [
        'name' => $params['name'],
        'firstName' => $params['firstName'],
        'dateNaissance' => $params['dateNaissance'],
        'address' => $params['address'],
        'npa' => $params['npa'],
        'equipe' => $params['equipe'],
    ];

    $existing_data[] = $nouveau_joueur;

    // Sauvegarder le fichier JSON mis à jour
    file_put_contents($file_path, json_encode($existing_data, JSON_PRETTY_PRINT));

    return new WP_REST_Response(['message' => 'Joueur ajouté avec succès'], 200);
}