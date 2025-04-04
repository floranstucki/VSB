<?php
namespace VSB\repositories;

class ComptaRepository {

    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function getComptaParEquipe($equipe_id, $saison_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare("
                SELECT 
                    p.pers_id, p.pers_prenom, p.pers_nom,
                    c.compt_id, c.compt_paye, c.compt_rabais_coti, c.compt_prix_cotisation,
                    c.compt_rabais_licence, c.compt_prix_licence, c.compt_frais_dentree,
                    c.compt_prix_tot, c.compt_sai_id
                FROM vsb_joueur j
                JOIN vsb_personne p ON j.jou_pers_id = p.pers_id
                JOIN vsb_joueurequipe je ON je.jouE_joueur_id = j.jou_id
                LEFT JOIN vsb_compta c ON c.compt_pers_id = p.pers_id AND c.compt_sai_id = %d
                WHERE je.jouE_equipe_id = %d
                ORDER BY p.pers_nom, p.pers_prenom
            ", $saison_id, $equipe_id)
        );
    }

    public function updateOrInsertCompta($data) {
        $existing_id = $this->wpdb->get_var($this->wpdb->prepare("
            SELECT compt_id FROM vsb_compta WHERE compt_pers_id = %d AND compt_sai_id = %d
        ", $data['compt_pers_id'], $data['compt_sai_id']));

        if ($existing_id) {
            return $this->wpdb->update('vsb_compta', $data, ['compt_id' => $existing_id]);
        } else {
            return $this->wpdb->insert('vsb_compta', $data);
        }
    }

    public function getComptaGlobale($saison_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare("
                SELECT p.pers_id, p.pers_prenom, p.pers_nom, c.*
                FROM vsb_compta c
                JOIN vsb_personne p ON c.compt_pers_id = p.pers_id
                WHERE c.compt_sai_id = %d
            ", $saison_id)
        );
    }

    public function getComptaParJoueur($pers_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare("
                SELECT c.*, s.sai_annee_debut, s.sai_annee_fin
                FROM vsb_compta c
                JOIN vsb_saison s ON c.compt_sai_id = s.sai_id
                WHERE c.compt_pers_id = %d
                ORDER BY s.sai_annee_debut DESC
            ", $pers_id)
        );
    }
}
