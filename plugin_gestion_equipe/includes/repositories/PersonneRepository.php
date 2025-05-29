<?php

class PersonneRepository
{



    public function __construct()
    {
    }


    public function creerPersonne($personne)
    {
        global $wpdb;

        $success = $wpdb->insert('vsb_personne', $personne, [
            '%s', // pers_nom
            '%s', // pers_prenom
            '%s', // pers_date_nai (date)
            '%s', // pers_sexe (ENUM)
            '%d', // pers_num_licence
            '%d', // pers_licence_ok (bool)
            '%s', // pers_licence_a
            '%s', // pers_nationalite_une
            '%s', // pers_nationalite_deux
            '%s', // pers_adresse
            '%s', // pers_NPA
            '%s', // pers_telephone
            '%s', // pers_mail
            '%s', // pers_num_avs
            '%s'  // pers_entree_club (date)
        ]);

        if (!$success) {
            error_log('Erreur MySQL : ' . $wpdb->last_error);
            error_log('Requête : ' . $wpdb->last_query);
        }

        if ($wpdb->insert_id) {
            return $wpdb->insert_id;
        }
        return false;
    }



    public function getAllPersonnes()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM vsb_personne", ARRAY_A);
    }

    public function getPersonneById($id)
    {
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM vsb_personne WHERE pers_id = %d", $id);
        return $wpdb->get_row($query, ARRAY_A);
    }

    public function getPersonnesNotJoueurs()
    {
        global $wpdb;
        $query = "
            SELECT p.*
            FROM vsb_personne p
            LEFT JOIN vsb_joueur j ON p.pers_id = j.jou_pers_id
            WHERE j.jou_pers_id IS NULL
        ";
        return $wpdb->get_results($query, ARRAY_A);
    }

    public function getPersonnesNotOtrs()
    {
        global $wpdb;
        $query = "
            SELECT p.*
            FROM vsb_personne p
            LEFT JOIN vsb_otr o ON p.pers_id = o.otr_pers_id
            WHERE o.otr_pers_id IS NULL
        ";
        return $wpdb->get_results($query, ARRAY_A);
    }


    public function modifierPersonne(int $id, array $data): bool
    {
        global $wpdb;

        // Vérifie que la personne existe
        $pers_exists = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM vsb_personne WHERE pers_id = %d", $id)
        );

        if (!$pers_exists) {
            error_log("Erreur : personne $id inexistante !");
            return false;
        }

        $format = array_map(function ($value) {
            return is_int($value) ? '%d' : '%s';
        }, $data);

        // Effectue la mise à jour
        return (bool) $wpdb->update(
            'vsb_personne',
            $data,
            ['pers_id' => $id],
            $format,
            ['%d']
        );
    }

    public function supprimerPersonne(int $id): bool
    {
        global $wpdb;
        return (bool) $wpdb->delete('vsb_personne', ['pers_id' => $id], ['%d']);
    }


}