<?php
global $wpdb;
class JoueurRepository
{



    public function __construct()
    {

    }
    public function creerJoueur($joueur)
    {
        global $wpdb;
        $formats = [
            '%d', // jou_pers_id
            '%d', // jou_num_maillot
            '%d', // jou_actif
            is_null($joueur['jou_pere']) ? '%s' : '%d',
            is_null($joueur['jou_mere']) ? '%s' : '%d',
        ];

        $success = $wpdb->insert('vsb_joueur', $joueur, $formats);
        if (!$success) {
            error_log('Erreur MySQL : ' . $wpdb->last_error);
            error_log('Requête : ' . $wpdb->last_query);
        }

        if ($wpdb->insert_id) {
            return $wpdb->insert_id;
        }
        return false;

    }



    public function getAllJoueurs()
    {
        global $wpdb;

        $query = "SELECT j.jou_id, j.jou_num_maillot, j.jou_actif, j.jou_pere, j.jou_mere, j.jou_pers_id, p.pers_id, p.pers_nom, p.pers_prenom, p.pers_date_nai, p.pers_adresse, p.pers_NPA, q.equ_cat FROM vsb_joueur j LEFT JOIN vsb_personne p ON p.pers_id = j.jou_pers_id LEFT JOIN vsb_joueurequipe je ON je.jouE_joueur_id = j.jou_id LEFT JOIN vsb_equipe q ON q.equ_id = je.jouE_equipe_id";

        return $wpdb->get_results($query, ARRAY_A);
    }


    public function getAllJoueursByEquipe($equipe)
    {
        global $wpdb;
        $query = $wpdb->prepare("SELECT j.jou_id, j.jou_num_maillot, j.jou_actif, j.jou_pere, j.jou_mere, j.jou_pers_id, p.pers_id, p.pers_nom, p.pers_prenom, p.pers_date_nai, p.pers_adresse, p.pers_NPA, q.equ_cat FROM vsb_joueur j LEFT JOIN vsb_personne p ON p.pers_id = j.jou_pers_id LEFT JOIN vsb_joueurequipe je ON je.jouE_joueur_id = j.jou_id LEFT JOIN vsb_equipe q ON q.equ_id = je.jouE_equipe_id  WHERE q.equ_cat = %s", $equipe);

        $joueurs = $wpdb->get_results($query, ARRAY_A);
        if (empty($joueurs)) {
            return null;
        }
        return $joueurs;
    }

    public function getJoueurById($id)
    {
        global $wpdb;
        $joueur = $wpdb->get_row(
            $wpdb->prepare("SELECT j.jou_id, j.jou_num_maillot, j.jou_actif, j.jou_pere, j.jou_mere, j.jou_pers_id, p.pers_id, p.pers_nom, p.pers_prenom, p.pers_date_nai, p.pers_adresse, p.pers_NPA,q.equ_id, q.equ_cat FROM vsb_joueur j LEFT JOIN vsb_personne p ON p.pers_id = j.jou_pers_id LEFT JOIN vsb_joueurequipe je ON je.jouE_joueur_id = j.jou_id LEFT JOIN vsb_equipe q ON q.equ_id = je.jouE_equipe_id WHERE j.jou_id = %d", $id),
            ARRAY_A
        );
        if (!empty($joueur)) {
            return $joueur;
        }
        return null;
    }


    public function modifierJoueur(int $id, array $data): bool
    {
        global $wpdb;
        // Vérifie si la personne existe dans vsb_personne
        $pers_id = (int) ($data['jou_pers_id'] ?? 0);
        if ($pers_id > 0) {
            $pers_exists = $wpdb->get_var(
                $wpdb->prepare("SELECT COUNT(*) FROM vsb_joueur WHERE jou_pers_id = %d", $pers_id)
            );

            if (!$pers_exists) {
                error_log("Erreur : personne $pers_id inexistante !");
                return false;
            }
        }

        $format = array_map(function ($value) {
            return is_int($value) ? '%d' : '%s';
        }, $data);

        return (bool) $wpdb->update(
            'vsb_joueur',
            $data,
            ['jou_pers_id' => $pers_id],
            $format,
            ['%d']
        );
    }

    public function modifierEquipeJoueur(array $data): bool
    {
        global $wpdb;

        $joueur_id = (int) ($data['jouE_joueur_id'] ?? 0);
        if ($joueur_id <= 0) {
            error_log("Erreur : ID du joueur invalide !");
            return false;
        }

        // Vérifie si le joueur existe dans la table vsb_joueur
        $joueur_exists = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM vsb_joueur WHERE jou_id = %d", $joueur_id)
        );

        if (!$joueur_exists) {
            error_log("Erreur : joueur $joueur_id inexistant !");
            return false;
        }

        // Déterminer les formats pour la mise à jour
        $format = array_map(function ($value) {
            return is_int($value) ? '%d' : '%s';
        }, $data);

        return (bool) $wpdb->update(
            'vsb_joueurequipe',            // nom de la table
            $data,                          // données à mettre à jour
            ['jouE_joueur_id' => $joueur_id], // condition WHERE
            $format,                        // format des données
            ['%d']                          // format de la clause WHERE
        );
    }



    public function supprimerJoueur(int $id): bool
    {
        global $wpdb;
        return (bool) $wpdb->delete('vsb_joueur', ['jou_id' => $id], ['%d']);
    }

    public function insererJoueurDansEquipe(int $joueur_id, int $equipe_id)
    {
        global $wpdb;
        $success = $wpdb->insert(
            'vsb_joueurequipe',
            [
                'jouE_joueur_id' => $joueur_id,
                'jouE_equipe_id' => $equipe_id
            ],
            [
                '%d', // Format pour joueur_id (entier)
                '%d'  // Format pour equipe_id (entier)
            ]
        );

        if (!$success) {
            error_log('Erreur MySQL : ' . $wpdb->last_error);
            error_log('Requête : ' . $wpdb->last_query);
        }
        if ($wpdb->insert_id) {
            return $wpdb->insert_id;
        }
        return false;
    }

}
?>