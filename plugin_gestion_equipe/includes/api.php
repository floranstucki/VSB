<?php

require_once plugin_dir_path(__FILE__) . 'controllers/JoueurController.php';
require_once plugin_dir_path(__FILE__) . 'repositories/JoueurRepository.php';
require_once plugin_dir_path(__FILE__) . 'controllers/OtrController.php';
require_once plugin_dir_path(__FILE__) . 'repositories/OtrRepository.php';
require_once plugin_dir_path(__FILE__) . 'controllers/PersonneController.php';
require_once plugin_dir_path(__FILE__) . 'repositories/PersonneRepository.php';
require_once plugin_dir_path(__FILE__) . 'controllers/EquipeController.php';
require_once plugin_dir_path(__FILE__) . 'repositories/EquipeRepository.php';

add_action('rest_api_init', function () {
    register_rest_route('gestion-equipe/v1', '/joueur/(?P<jou_id>\d+)', [
        'methods' => 'GET',
        'callback' => 'get_joueur',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('gestion-equipe/v1', '/joueurs', [
        'methods' => 'GET',
        'callback' => 'list_joueurs',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('gestion-equipe/v1', '/equipes', [
        'methods' => 'GET',
        'callback' => 'list_equipes',
        'permission_callback' => '__return_true'
    ]);

     register_rest_route('gestion-equipe/v1', '/equipessaison', [
        'methods' => 'GET',
        'callback' => 'list_equipes_saison',
        'permission_callback' => '__return_true'
    ]);


    register_rest_route('gestion-equipe/v1', '/joueurs/(?P<equipe>[a-zA-Z0-9_-]+)', [
        'methods' => 'GET',
        'callback' => 'list_joueurs_equipes',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('gestion-equipe/v1', '/personnes', [
        'methods' => 'GET',
        'callback' => 'list_personnes',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('gestion-equipe/v1', '/personnesjoueurs', [
        'methods' => 'GET',
        'callback' => 'list_personnes_joueurs',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('gestion-equipe/v1', '/personnesotrs', [
        'methods' => 'GET',
        'callback' => 'list_personnes_otrs',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('gestion-equipe/v1', '/otr/(?P<otr_id>\d+)', [
        'methods' => 'GET',
        'callback' => 'get_otr',
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('gestion-equipe/v1', '/otrs', [
        'methods' => 'GET',
        'callback' => 'list_otrs',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('gestion-equipe/v1', '/personne/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'get_personne',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('gestion-equipe/v1', '/addjoueur', array(
        'methods' => 'POST',
        'callback' => 'create_joueur',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('gestion-equipe/v1', '/addpersonne', array(
        'methods' => 'POST',
        'callback' => 'create_personne',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('gestion-equipe/v1', '/addotr', array(
        'methods' => 'POST',
        'callback' => 'create_otr',
        'permission_callback' => '__return_true'
    ));


    register_rest_route('gestion-equipe/v1', '/joueur/(?P<jou_id>\d+)', [
        'methods' => 'POST',
        'callback' => 'update_joueur',
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('gestion-equipe/v1', '/otr/(?P<otr_id>\d+)', [
        'methods' => 'POST',
        'callback' => 'update_otr',
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('gestion-equipe/v1', '/personne/(?P<pers_id>\d+)', [
        'methods' => 'POST',
        'callback' => 'update_personne',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('gestion-equipe/v1', '/joueur/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'supprimer_joueur',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('gestion-equipe/v1', '/otr/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'supprimer_otr',
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('gestion-equipe/v1', '/personne/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'supprimer_personne',
        'permission_callback' => '__return_true'
    ]);


});

function list_equipes()
{
    $controller = new EquipeController(new EquipeRepository());
    return new WP_REST_Response($controller->getEquipes(), 200);
}

function list_equipes_saison()
{
    $controller = new EquipeController(new EquipeRepository());
    return new WP_REST_Response($controller->getEquipesSaisonEnCours(), 200);
}

/**
 * Récupération d'un joueur via son ID
 * @param WP_REST_Request $data -> contient les données du formulaire
 * @return WP_REST_Response -> retourne le joueur avec le code 200 ou message d'erreur avec code 404
 */
function get_joueur(WP_REST_Request $data)
{
    $id = (int) $data->get_param('jou_id');

    $controller = new JoueurController(new JoueurRepository());
    $joueur = $controller->getJoueurById($id);

    if ($joueur) {
        return new WP_REST_Response($joueur, 200);
    }
    return new WP_REST_Response(['message' => 'Joueur introuvable'], 404);
}

function get_otr(WP_REST_Request $data)
{
    $id = (int) $data->get_param('otr_id');

    $controller = new OtrController(new OtrRepository());
    $otr = $controller->getOTRById($id);

    if ($otr) {
        return new WP_REST_Response($otr, 200);
    }
    return new WP_REST_Response(['message' => 'OTR introuvable'], 404);
}

/**
 * Récupération d'une personne via son ID
 * @param mixed $data -> contient les données du formulaire
 * @return WP_REST_Response -> retourne la personne avec le code 200 ou message d'erreur avec code 404
 */
function get_personne($data)
{
    $controller = new PersonneController(new PersonneRepository());
    $personne = $controller->getPersonneById((int) $data['id']);

    if ($personne) {
        return new WP_REST_Response($personne, 200);
    }
    return new WP_REST_Response(['message' => 'Joueur introuvable'], 404);
}

/**
 * Récupération de tous les joueurs
 * @return WP_REST_Response
 */
function list_joueurs()
{
    $controller = new JoueurController(new JoueurRepository());
    return new WP_REST_Response($controller->getAllJoueurs(), 200);
}

function list_joueurs_equipes($data)
{
    $controller = new JoueurController(new JoueurRepository());
    return new WP_REST_Response($controller->getAllJoueursByEquipe($data['equipe']), 200);
}

/**
 * Récupération de toutes les personnes
 * @return WP_REST_Response
 */
function list_personnes()
{
    $controller = new PersonneController(new PersonneRepository());
    return new WP_REST_Response($controller->getAllPersonnes(), 200);
}

/**
 * Récupération de toutes les personnes qui ne sont pas joueurs
 * @return WP_REST_Response
 */
function list_personnes_joueurs()
{
    $controller = new PersonneController(new PersonneRepository());
    return new WP_REST_Response($controller->getPersonnesNotJoueurs(), 200);
}

/**
 * Récupération de toutes les personnes qui ne sont pas OTR
 * @return WP_REST_Response
 */
function list_personnes_otrs()
{
    $controller = new PersonneController(new PersonneRepository());
    return new WP_REST_Response($controller->getPersonnesNotOtrs(), 200);
}

/**
 * Récupération de tous les otrs
 * @return WP_REST_Response
 */
function list_otrs()
{
    $controller = new OtrController(new OtrRepository());
    return new WP_REST_Response($controller->getAllOTR(), 200);
}


/**
 * Fonction pour créer un joueur pour le veyrier salève basket, si la personne n'est pas créée avant de créer le joueur, cette dernière ce crééra avant de créer le joueur
 * @param WP_REST_Request $request -> contient toutes les données envoyées par le formulaire.
 * @return WP_REST_Response -> réponse personnalisée pour l'utilisateur
 */
function create_joueur(WP_REST_Request $request)
{
    $controllerP = new PersonneController(new PersonneRepository());
    $controllerJ = new JoueurController(new JoueurRepository());

    $data = $request->get_params();
    unset($data['_wpnonce']);
    unset($data['_wp_http_referer']);
    if (!is_array($data)) {
        return new WP_REST_Response(['message' => 'Données invalides'], 400);
    }

    if (empty($data['personne'])) {
        $idPersonne = $controllerP->creerPersonne($data);

        $data['personne'] = $idPersonne;
    }

    $id = $controllerJ->creerJoueur($data);

    $controllerJ->insererJoueurDansEquipe($id, (int) $data['equipe']);
    if ($id !== false && $id > 0) {
        return new WP_REST_Response(['message' => 'Joueur créé', 'id' => $id], 201);
    } else {
        return new WP_REST_Response(['message' => 'Création du joueur impossible'], 400);
    }


}
/**
 * Fonction pour créer une personne pour le veyrier salève basket
 * @param WP_REST_Request $request -> contient toutes les données envoyées par le formulaire.
 * @return WP_REST_Response -> réponse personnalisée pour l'utilisateur
 */
function create_personne(WP_REST_Request $request)
{
    $controller = new PersonneController(new PersonneRepository());

    $data = $request->get_params();

    if (!is_array($data)) {
        return new WP_REST_Response(['message' => 'Données invalides'], 400);
    }

    error_log('Données reçues : ' . print_r($data, true));

    $id = $controller->creerPersonne($data);

    if (!$id) {
        return new WP_REST_Response(['message' => 'Erreur lors de la création de la personne'], 400);
    }
    return new WP_REST_Response(['message' => 'Personne créée', 'id' => $id], 201);
}

/**
 * Fonction pour créer un otr pour le veyrier salève basket, si la personne n'est pas créée avant de créer l'otr, cette dernière ce crééra avant de créer l'otr
 * @param WP_REST_Request $request -> contient toutes les données envoyées par le formulaire.
 * @return WP_REST_Response -> réponse personnalisée pour l'utilisateur
 */
function create_otr(WP_REST_Request $request)
{
    $controllerP = new PersonneController(new PersonneRepository());
    $controllerO = new OtrController(new OtrRepository());

    $data = $request->get_params();
    unset($data['_wpnonce']);
    unset($data['_wp_http_referer']);
    if (!is_array($data)) {
        return new WP_REST_Response(['message' => 'Données invalides'], 400);
    }
    if (empty($data['personne'])) {
        $idPersonne = $controllerP->creerPersonne($data);

        $data['personne'] = $idPersonne;
    }
    // Création du joueur
    $idOTR = $controllerO->creerOTR($data);
    error_log('Retour de l\'insertion : ' . print_r($idOTR, true));


    if (!empty($data['equipes']) && is_array($data['equipes'])) {
            error_log('Retour de l\'insertion : ' . print_r($data['equipes'], true));

        foreach ($data['equipes'] as $idEquipe) {
            $controllerO->insererOTREquipe($idOTR, $idEquipe);
        }
    }

    if ($idOTR !== false && $idOTR > 0) {
        return new WP_REST_Response(['message' => 'OTR créé', 'id' => $idOTR], 201);
    } else {
        return new WP_REST_Response(['message' => 'Création de l\'OTR impossible'], 400);
    }


}

/**
 * Modification des informations d'un joueur
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function update_joueur(WP_REST_Request $request)
{
    error_log(print_r($request, true));
    $controller = new JoueurController(new JoueurRepository());
    $id = (int) $request['jou_id'];
    // Récupération des données multipart/form-data
    $data = $request->get_params();

    // Vérifie le contenu reçu
   


    $success = $controller->modifierJoueur($id, $data);
    if ($success) {

        $controller->modifierEquipeJoueur($id, $data['equipe']);
        return new WP_REST_Response(['message' => 'Joueur mis à jour'], 200);
    }
    return new WP_REST_Response(['message' => 'Erreur de mise à jour'], 400);
}


function update_otr(WP_REST_Request $request)
{
    error_log(print_r($request, true));
    $controller = new OtrController(new OtrRepository());
    $id = (int) $request['otr_id'];
    // Récupération des données multipart/form-data
    $data = $request->get_params();


    $success = $controller->modifierOTR($id, $data);
    if ($success) {
        return new WP_REST_Response(['message' => 'OTR mis à jour'], 200);
    }
    return new WP_REST_Response(['message' => 'Erreur de mise à jour'], 400);
}

function update_personne(WP_REST_Request $request)
{
    error_log(print_r($request, true));
    $controller = new PersonneController(new PersonneRepository());
    $id = (int) $request['pers_id'];
    // Récupération des données multipart/form-data
    $data = $request->get_params();

    $success = $controller->modifierPersonne($id, $data);
    if ($success) {
        return new WP_REST_Response(['message' => 'Personne mise à jour'], 200);
    }
    return new WP_REST_Response(['message' => 'Erreur de mise à jour'], 400);
}



/**
 * Suppression d'un joueur via son ID, demande via le formulaire avant de supprimer le joueur
 * @param mixed $data
 * @return WP_REST_Response
 */
function supprimer_joueur($data)
{
    $controller = new JoueurController(new JoueurRepository());

    $joueur_id = (int) $data['id'];
    $success = $controller->supprimerJoueur($joueur_id);

    if ($success) {
        return new WP_REST_Response(['message' => 'Joueur supprimé avec succès'], 200);
    } else {
        return new WP_REST_Response(['message' => 'Erreur : joueur introuvable ou non supprimé'], 500);
    }
}

function supprimer_otr($data)
{
    $controller = new OtrController(new OtrRepository());

    $otr_id = (int) $data['id'];
    $success = $controller->supprimerOTR($otr_id);

    if ($success) {
        return new WP_REST_Response(['message' => 'OTR supprimé avec succès'], 200);
    } else {
        return new WP_REST_Response(['message' => 'Erreur : OTR introuvable ou non supprimé'], 500);
    }
}

function supprimer_personne($data)
{
    $controller = new PersonneController(new PersonneRepository());

    $otr_id = (int) $data['id'];
    $success = $controller->supprimerPersonne($otr_id);

    if ($success) {
        return new WP_REST_Response(['message' => 'Personne supprimé avec succès'], 200);
    } else {
        return new WP_REST_Response(['message' => 'Erreur : Personne introuvable ou non supprimé'], 500);
    }
}


