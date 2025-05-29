<?php
namespace VSB\repositories;

class CalendrierRepository {

    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function getEvenementsMatchs() {
        $resultats = $this->wpdb->get_results("
            SELECT 
                c.conf_id AS id,
                e.equ_cat AS equipe,
                c.conf_date AS date,
                c.conf_heure AS heure,
                c.conf_lieu AS lieu,
                c.conf_adversaire AS adversaire,
                c.conf_score_equipe,
                c.conf_score_adverse
            FROM vsb_confrontation c
            JOIN vsb_equipe e ON c.conf_equ_id = e.equ_id
        ");
    
        $evenements = [];
        foreach ($resultats as $r) {
            $datetime = $r->heure ? "{$r->date}T{$r->heure}" : $r->date;
            $evenements[] = [
                'id' => $r->id,
                'title' => "{$r->equipe} vs {$r->adversaire}",
                'start' => $datetime,
                'location' => $r->lieu,
                'score_equipe' => $r->conf_score_equipe,
                'score_adverse' => $r->conf_score_adverse,
                'is_past' => ($r->date < date('Y-m-d')) ? true : false
            ];
        }
        return $evenements;
    }
    
}
