<?php

global $wpdb;

class EquipeRepository
{
    public function __construct()
    {
        // Initialisation si nécessaire
    }

    public function getEquipesSaisonEnCours()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT e.equ_id, e.equ_cat FROM vsb_equipe e LEFT JOIN vsb_saison s ON e.equ_sai_id = s.sai_id  WHERE s.sai_enCours = 1", ARRAY_A);
    }

    public function getAllEquipes()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT e.equ_cat, COUNT(j.jou_id) AS nombre_joueurs FROM vsb_equipe e LEFT JOIN vsb_joueurequipe o ON o.jouE_equipe_id = e.equ_id LEFT JOIN vsb_joueur j ON j.jou_id = o.jouE_joueur_id GROUP BY e.equ_id", ARRAY_A);
    }

    public function getEquipeById($id)
    {
        global $wpdb;
        $equipe = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM vsb_equipe WHERE equ_id = %d", $id),
            ARRAY_A
        );
        return $equipe ?: null;
    }

    

}