<?php
namespace VSB\repositories;

class RessourceRepository {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function getAll() {
        return $this->wpdb->get_results("SELECT * FROM vsb_ressource ORDER BY ress_date_creation DESC");
    }

    public function ajouter($titre, $desc, $type, $url, $coach_id) {
        return $this->wpdb->insert('vsb_ressource', [
            'ress_titre' => $titre,
            'ress_desc' => $desc,
            'ress_type' => $type,
            'ress_url_fichier' => $url,
            'ress_coach_id' => $coach_id
        ]);
    }

    public function supprimer($id) {
        $url = $this->wpdb->get_var("SELECT ress_url_fichier FROM vsb_ressource WHERE ress_id = $id");
    
        // Convertir l’URL en chemin absolu
        $chemin = str_replace(plugins_url(), ABSPATH, $url);
        if (file_exists($chemin)) {
            unlink($chemin); // supprime le fichier
        }
    
        return $this->wpdb->delete('vsb_ressource', ['ress_id' => $id]);
    }
    

    public function getCoachIdByUserEmail($email) {
        return $this->wpdb->get_var("
            SELECT coa.coa_id
            FROM vsb_coach coa
            JOIN vsb_personne p ON coa.coa_pers_id = p.pers_id
            WHERE p.pers_mail = " . $this->wpdb->prepare('%s', $email)
        );
    }
    public function getFiltered($type, $coach, $recherche) {
        $sql = "SELECT r.*, p.pers_prenom, p.pers_nom
                FROM vsb_ressource r
                LEFT JOIN vsb_coach c ON c.coa_id = r.ress_coach_id
                LEFT JOIN vsb_personne p ON p.pers_id = c.coa_pers_id
                WHERE 1=1";
    
        $args = [];
    
        if ($type) {
            $sql .= " AND r.ress_type = %s";
            $args[] = $type;
        }
    
        if ($coach) {
            $sql .= " AND r.ress_coach_id = %d";
            $args[] = $coach;
        }
    
        if ($recherche) {
            $sql .= " AND r.ress_titre LIKE %s";
            $args[] = '%' . $recherche . '%';
        }
    
        $sql .= " ORDER BY r.ress_date_creation DESC";
    
        return $this->wpdb->get_results($this->wpdb->prepare($sql, ...$args));
    }
    
    public function getListeCoachs() {
        return $this->wpdb->get_results("
            SELECT c.coa_id, p.pers_nom, p.pers_prenom
            FROM vsb_coach c
            JOIN vsb_personne p ON c.coa_pers_id = p.pers_id
            ORDER BY p.pers_nom
        ");
    }
    public function getById($id) {
        return $this->wpdb->get_row("SELECT * FROM vsb_ressource WHERE ress_id = " . intval($id));
    }
    
    public function modifier($id, $titre, $desc, $type) {
        return $this->wpdb->update('vsb_ressource', [
            'ress_titre' => $titre,
            'ress_desc' => $desc,
            'ress_type' => $type
        ], ['ress_id' => $id]);
    }
    
}
