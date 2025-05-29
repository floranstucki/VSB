<?php
namespace VSB\repositories;

class StatsRepository {

    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function getStatsParMatch($match_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare("
                SELECT s.*, p.pers_prenom, p.pers_nom
                FROM vsb_statsjoueur s
                JOIN vsb_joueur j ON s.stats_joueur_id = j.jou_id
                JOIN vsb_personne p ON j.jou_pers_id = p.pers_id
                WHERE s.stats_match_id = %d
            ", $match_id)
        );
    }

    public function getStatById($stats_id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM vsb_statsjoueur WHERE stats_id = %d", $stats_id)
        );
    }

    public function updateStat($id, $data) {
        $this->wpdb->update('vsb_statsjoueur', $data, ['stats_id' => $id]);
    }

    public function getStatsJoueurSaison($joueur_id, $saison_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare("
                SELECT s.*, c.conf_date, c.conf_adversaire
                FROM vsb_statsjoueur s
                JOIN vsb_confrontation c ON s.stats_match_id = c.conf_id
                JOIN vsb_equipe e ON c.conf_equ_id = e.equ_id
                WHERE s.stats_joueur_id = %d AND e.equ_sai_id = %d
                AND s.stats_tempsDeJeu > 0
                ORDER BY c.conf_date ASC
            ", $joueur_id, $saison_id)
        );
    }
}
