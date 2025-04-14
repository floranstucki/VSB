<?php
add_action('rest_api_init', 'create_rest_endpoint');
add_action('rest_api_init', 'update_rest_endpoint');
add_action('rest_api_init', function () {
    register_rest_route('gestion-equipe/v1', '/supprimer-joueur/(?P<id>\d+)', [
        'methods'  => 'DELETE',
        'callback' => 'supprimer_joueur',
         // À sécuriser si besoin 
    ]);
});

function create_rest_endpoint(){
    register_rest_route('v1', '/ajout-joueur-form/submit',array(
        'methods' => 'POST',
        'callback' => 'ajouter_joueur'
    ));
}

add_action('rest_api_init', function () {
    register_rest_route('v1', '/equipes', array(
        'methods'  => 'GET',
        'callback' => 'obtenir_equipes',
    ));
});

function update_rest_endpoint(){
    error_log('Api rest enregistrée');
    register_rest_route('v1', '/modifier-joueur-form/submit', array(
        'methods' => 'POST',
        'callback' => 'modifier_joueur',
        'permission_callback' => '__return_true'
    )); 
}

function obtenir_joueurs() {
    global $wpdb;

    $table_joueur = 'vsb_joueur';
    $table_personne = 'vsb_personne';
    $table_joueurEq = 'vsb_joueurequipe';
    $table_equipe = 'vsb_equipe';

    // Requête SQL
    $sql = "SELECT * FROM $table_joueur
                    JOIN $table_personne on $table_personne.pers_id = $table_joueur.jou_pers_id
                    JOIN $table_joueurEq on $table_joueurEq.joue_joueur_id = $table_joueur.jou_id
                    JOIN $table_equipe on $table_joueurEq.joue_equipe_id = $table_equipe.equ_id";

    // Exécuter la requête
    $joueurs = $wpdb->get_results($sql, ARRAY_A);

    return $joueurs;
}

function obtenir_equipes() {
    global $wpdb;

    // Requête SQL
    $sql = "SELECT * FROM vsb_equipe";

    // Exécuter la requête
    $equipes = $wpdb->get_results($sql, ARRAY_A);

    // Vérifie si la liste des équipes est vide
    if(!$equipes) {
        return new WP_REST_Response(['message' => 'Aucune équipe trouvée.'], 404);
    }

    return $equipes;
}

function obtenir_personnes() {
    global $wpdb;

    // Requête SQL
    $sql = "SELECT * FROM vsb_personne";

    // Exécuter la requête
    $joueurs = $wpdb->get_results($sql, ARRAY_A);

    return $joueurs;
}

function obtenir_equipe_nom(){

    global $wpdb;

    $sql = "SELECT * 
    FROM vsb_equipe
    JOIN vsb_JoueurEquipe ON jouE_equipe_id = equ_id
    JOIN vsb_Joueur ON jou_id = jouE_joueur_id
    JOIN vsb_personne ON pers_id = jou_pers_id
    WHERE equ_cat = 'U08B'"; 
    $equipe = $wpdb->get_results($sql, ARRAY_A);

    return $equipe;
}

function supprimer_joueur($data) {
    global $wpdb;
    $joueur_id = intval($data['id']);  // On s'assure que l'ID est bien un entier

    // Vérification de l'ID du joueur
    if ($joueur_id <= 0) {
        return new WP_REST_Response(['message' => 'ID de joueur invalide.'], 400);
    }

    // Définir le nom des tables sans préfixe
    $table_joueur = 'vsb_joueur';
    $table_joueurEq = 'vsb_joueurequipe';
    $table_personne = 'vsb_personne';

    // Récupère l'ID de la personne associée au joueur
    $pers_id = $wpdb->get_var(
        $wpdb->prepare("SELECT jou_pers_id FROM $table_joueur WHERE jou_id = %d", $joueur_id)
    );

    if (!$pers_id) {
        return new WP_REST_Response(['message' => 'Joueur non trouvé.'], 404);
    }

    // Supprimer les liens joueur-équipe
    $wpdb->delete($table_joueurEq, ['joue_joueur_id' => $joueur_id]);

    // Supprimer le joueur
    $deleted_joueur = $wpdb->delete($table_joueur, ['jou_id' => $joueur_id]);

    // Supprimer la personne associée
    $deleted_personne = $wpdb->delete($table_personne, ['pers_id' => $pers_id]);

    if ($deleted_joueur && $deleted_personne) {
        return new WP_REST_Response(['message' => 'Joueur et personne supprimés avec succès.'], 200);
    } else {
        return new WP_REST_Response(['message' => 'Erreur lors de la suppression.'], 500);
    }
}

function ajouter_joueur($data) {
    global $wpdb;

    // Récupérer les données du joueur
    $params = $data->get_params();

    // Vérification du nonce pour la sécurité
    if (!wp_verify_nonce($params['_wpnonce'], 'wp_rest')) {
        return new WP_REST_Response('Message not sent', 422);
    }

    // Suppression des paramètres inutiles
    unset($params['_wpnonce']);
    unset($params['_wp_http_referer']);

    // Définir le nom des tables sans préfixe
    $table_joueur = 'vsb_joueur';
    $table_personne = 'vsb_personne';
    $table_joueurEq = 'vsb_joueurequipe';

    // Insérer la personne dans la table vsb_personne
    $personne_data = [
        'pers_nom' => $params['name'],
        'pers_prenom' => $params['firstName'],
        'pers_date_nai' => $params['dateNaissance'],
        'pers_adresse' => $params['address'],
        'pers_NPA' => $params['npa'],
    ];

    $wpdb->insert($table_personne, $personne_data);
    $pers_id = $wpdb->insert_id; // Récupérer l'ID de la personne insérée

    if (!$pers_id) {
        return new WP_REST_Response('Erreur lors de l\'insertion de la personne', 500);
    }

    // Insérer le joueur dans la table vsb_joueur
    $joueur_data = [
        'jou_pers_id' => $pers_id,
    ];
    $wpdb->insert($table_joueur, $joueur_data);
    $joueur_id = $wpdb->insert_id; // Récupérer l'ID du joueur inséré

    if (!$joueur_id) {
        return new WP_REST_Response('Erreur lors de l\'insertion du joueur', 500);
    }
    // Insérer le lien joueur-équipe dans la table vsb_joueurequipe
    $joueurEq_data = [
        'joue_joueur_id' => $joueur_id,
        'joue_equipe_id' => $params['equipe'],
    ];
    $wpdb->insert($table_joueurEq, $joueurEq_data);
    $joueurEq_id = $wpdb->insert_id; // Récupérer l'ID du lien joueur-équipe inséré

    return new WP_REST_Response(['message' => 'Joueur ajouté avec succès.'], 200);

}

function modifier_joueur($request){
    $params = $request->get_params();
    global $wpdb;
    $jou_id = (int) $params['jou_id'];

    $pers_id = $wpdb->get_var($wpdb->prepare(
        "SELECT jou_pers_id FROM vsb_joueur WHERE jou_id = %d",
        $jou_id
    ));
    
    if (!$pers_id) {
        return new WP_REST_Response(['message' => 'Joueur non trouvé'], 404);
    }
    $pers_nom = sanitize_text_field($params['pers_nom']);
    $pers_prenom = sanitize_text_field($params['pers_prenom']);
    $pers_date_nai = sanitize_text_field($params['pers_date_nai']);
    $pers_adresse = sanitize_text_field($params['pers_adresse']);
    $pers_NPA = sanitize_text_field($params['pers_NPA']);
    $jou_equipe = sanitize_text_field($params['equipe']);

    

    // Mettre à jour la table personne
    $wpdb->update(
        'vsb_personne',
        [
            'pers_nom' => $pers_nom,
            'pers_prenom' => $pers_prenom,
            'pers_date_nai' => $pers_date_nai,
            'pers_adresse' => $pers_adresse,
            'pers_NPA' => $pers_NPA
        ],
        ['pers_id' => $pers_id],
        ['%s', '%s', '%s', '%s', '%s'],
        ['%d']
    );

    // Mettre à jour la table joueur
    /*$wpdb->update(
        $wpdb->prefix . 'joueur',
        [
            'jou_num_maillot' => $jou_num_maillot
        ],
        ['jou_id' => $jou_id],
        ['%d'],
        ['%d']
    );*/

    // Mettre à jour la table joueurEquipe
    $equipes_table = 'vsb_joueurequipe';
    $wpdb->query($wpdb->prepare("DELETE FROM $equipes_table WHERE jouE_joueur_id = %d", $jou_id));
    $wpdb->insert(
        $equipes_table,
        [
            'jouE_joueur_id' => $jou_id,
            'jouE_equipe_id' => $jou_equipe
        ],
        ['%d', '%s']
    );

    error_log('MODIFICATION: ' . print_r($params, true));

    return new WP_REST_Response(['message' => 'Joueur modifié avec succès'], 200);
}

