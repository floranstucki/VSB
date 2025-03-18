<?php

add_action('rest_api_init', 'delete_rest_endpoint');

function delete_rest_endpoint(){
    register_rest_route('gestion-equipe/v1', '/supprimer-joueur/(?P<index>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'supprimer_joueur',
        'permission_callback' => '__return_true'
    ]);
}

function supprimer_joueur($data) {
    $file_path = plugin_dir_path(__FILE__) . 'joueur_form.json';

    if (!file_exists($file_path)) {
        return new WP_REST_Response(['message' => 'Fichier introuvable'], 404);
    }

    $json_data = file_get_contents($file_path);
    $joueurs = json_decode($json_data, true);

    if (!is_array($joueurs)) {
        return new WP_REST_Response(['message' => 'Erreur de lecture du fichier'], 500);
    }

    $index = (int) $data['index'];

    if (!isset($joueurs[$index])) {
        return new WP_REST_Response(['message' => 'Joueur introuvable'], 404);
    }

    // Supprimer le joueur
    array_splice($joueurs, $index, 1);

    // Sauvegarder la nouvelle liste dans le fichier JSON
    file_put_contents($file_path, json_encode($joueurs, JSON_PRETTY_PRINT));

    return new WP_REST_Response(['message' => 'Joueur supprimé avec succès'], 200);
}