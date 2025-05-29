<?php
namespace VSB\repositories;

class MatchRepository {

    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function creerConfrontation($data): int|false {
        $this->wpdb->insert('vsb_confrontation', $data, [
            '%d', '%s', '%s', '%s', '%s', '%d', '%d'
        ]);
        return $this->wpdb->insert_id ?: false;
    }

    public function getMatchById($match_id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM vsb_confrontation WHERE conf_id = %d",
                $match_id
            ),
            ARRAY_A
        );
    }

    public function getMatchsByEquipe($equipe_id): array {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM vsb_confrontation WHERE conf_equ_id = %d ORDER BY conf_date DESC",
                $equipe_id
            ),
            ARRAY_A
        );
    }

    public function modifierMatch($match_id, $data): void {
        $this->wpdb->update(
            'vsb_confrontation',
            $data,
            ['conf_id' => $match_id],
            ['%s', '%s', '%s'],
            ['%d']
        );
    }

    public function supprimerMatchEtStats($match_id): void {
        $this->wpdb->delete('vsb_statsjoueur', ['stats_match_id' => $match_id]);
        $this->wpdb->delete('vsb_confrontation', ['conf_id' => $match_id]);
    }

    public function getEquipeLabel($equipe_id): ?string {
        return $this->wpdb->get_var(
            $this->wpdb->prepare("SELECT equ_cat FROM vsb_equipe WHERE equ_id = %d", $equipe_id)
        );
    }

    public function getJoueursDeLEquipe($equipe_id): array {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT j.jou_id, p.pers_nom, p.pers_prenom
                 FROM vsb_joueur j
                 JOIN vsb_joueurequipe je ON je.jouE_joueur_id = j.jou_id
                 JOIN vsb_personne p ON j.jou_pers_id = p.pers_id
                 WHERE je.jouE_equipe_id = %d",
                $equipe_id
            )
        );
    }
}
