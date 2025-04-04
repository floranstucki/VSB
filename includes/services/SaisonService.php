<?php
namespace VSB\services;

class SaisonService {
    public function getSaisonEnCoursId() {
        global $wpdb;
        return $wpdb->get_var("SELECT sai_id FROM vsb_saison WHERE sai_enCours = 1 LIMIT 1");
    }

    public function getIdSaisonEnCours(): ?int {
        global $wpdb;
        return $wpdb->get_var("SELECT sai_id FROM vsb_saison WHERE sai_enCours = 1");
    }

    public function getEquipesSaisonEnCours(): array {
        global $wpdb;
        $saison_id = $this->getIdSaisonEnCours();
        return $wpdb->get_results(
            $wpdb->prepare("SELECT equ_id, equ_cat FROM vsb_equipe WHERE equ_sai_id = %d", $saison_id),
            ARRAY_A
        );
    }

    public function getSaisonLabelEnCours(): string {
        global $wpdb;
        $row = $wpdb->get_row("SELECT sai_annee_debut, sai_annee_fin FROM vsb_saison WHERE sai_enCours = 1");
        return $row ? "{$row->sai_annee_debut}–{$row->sai_annee_fin}" : 'Non défini';
    }
}
