<?php
namespace VSB\repositories;

class OTRRepository {

    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function getOtrsParEquipe($equipe_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare("
                SELECT o.*, p.pers_prenom, p.pers_nom, p.pers_mail
                FROM vsb_otr o
                JOIN vsb_personne p ON o.otr_pers_id = p.pers_id
                JOIN vsb_otrequipe oe ON oe.id_otr = o.otr_id
                WHERE oe.id_eq = %d
            ", $equipe_id)
        );
    }

    public function assignerOTR($match_id, $otr_id, $role) {
        // Écrase si déjà existant
        $this->wpdb->query(
            $this->wpdb->prepare("
                DELETE FROM vsb_otrsurmatch WHERE osm_match_id = %d AND osm_role = %s
            ", $match_id, $role)
        );
        return $this->wpdb->insert('vsb_otrsurmatch', [
            'osm_match_id' => $match_id,
            'osm_otr_id' => $otr_id,
            'osm_role' => $role
        ]);
    }

    public function getOTRAssignes($match_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare("
                SELECT osm.osm_role, p.pers_prenom, p.pers_nom
                FROM vsb_otrsurmatch osm
                JOIN vsb_otr o ON osm.osm_otr_id = o.otr_id
                JOIN vsb_personne p ON o.otr_pers_id = p.pers_id
                WHERE osm.osm_match_id = %d
            ", $match_id)
        );
    }

    public function getMatchsAvecMoinsDe2Otr() {
        return $this->wpdb->get_results("
            SELECT c.*, e.equ_cat, COUNT(osm.osm_otr_id) AS total_otr
            FROM vsb_confrontation c
            JOIN vsb_equipe e ON c.conf_equ_id = e.equ_id
            LEFT JOIN vsb_otrsurmatch osm ON c.conf_id = osm.osm_match_id
            WHERE c.conf_date >= CURDATE()
            GROUP BY c.conf_id
            HAVING total_otr < 2
            ORDER BY e.equ_cat, c.conf_date
        ");
    }

    public function getInfosMatch($match_id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("
                SELECT c.*, e.equ_cat
                FROM vsb_confrontation c
                JOIN vsb_equipe e ON c.conf_equ_id = e.equ_id
                WHERE c.conf_id = %d
            ", $match_id)
        );
    }
}
