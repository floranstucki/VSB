<?php

class OtrRepository
{


    public function __construct()
    {
    }

    public function creerOTR(array $data): int|false
    {
        global $wpdb;
        $wpdb->insert('vsb_otr', $data, [
            '%d', // otr_pers_id
            '%s', // otr_niveau_otr
        ]);
        if ($wpdb->insert_id) {
            return $wpdb->insert_id;
        }
        return false;
    }

    public function getOTRById(int $id): ?array
    {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT o.otr_id, o.otr_pers_id, o.otr_niveau_otr, p.pers_id, p.pers_nom, p.pers_prenom FROM vsb_otr o LEFT JOIN vsb_personne p ON o.otr_pers_id = p.pers_id  WHERE otr_id = %d", $id),
            ARRAY_A
        );
        return $row ?: null;
    }

    public function modifierOTR(int $id, array $data): bool
    {
        global $wpdb;

        // Vérifie si l'OTR existe
        $otr_exists = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM vsb_otr WHERE otr_id = %d", $id)
        );

        if (!$otr_exists) {
            error_log("Erreur : OTR avec l'ID $id inexistant !");
            return false;
        }

        // Générer dynamiquement le format des valeurs (type int = %d, string = %s, etc.)
        $formats = [];
        foreach ($data as $value) {
            $formats[] = is_int($value) ? '%d' : '%s';
        }

        $success = $wpdb->update(
            'vsb_otr',
            $data,
            ['otr_id' => $id],
            $formats,
            ['%d']
        );

        if ($success === false) {
            error_log("Erreur MySQL : " . $wpdb->last_error);
            return false;
        }

        return true;
    }


    public function supprimerOTR(int $id): bool
    {
        global $wpdb;
        return (bool) $wpdb->delete('vsb_otr', ['otr_id' => $id], ['%d']);
    }

    public function getAllOTR(): array
    {
        global $wpdb;
        return $wpdb->get_results("SELECT o.otr_id, o.otr_pers_id, o.otr_niveau_otr, p.pers_nom, p.pers_prenom FROM vsb_otr o LEFT JOIN vsb_personne p ON o.otr_pers_id = p.pers_id", ARRAY_A);
    }


    public function insererOTREquipe($idOTR, $idEquipe){
        global $wpdb;
        $success = $wpdb->insert(
            'vsb_otrequipe',
            [
                'id_otr' => $idOTR,
                'id_eq' => $idEquipe
            ],
            [
                '%d', 
                '%d'  
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
