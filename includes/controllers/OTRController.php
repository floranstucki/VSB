<?php
namespace VSB\controllers;

use VSB\repositories\OTRRepository;
use VSB\services\MailerService;

class OTRController {

    public function registerShortcodes() {
        add_shortcode('vsb_gestion_otr', [$this, 'afficherGestionOtr']);
        add_shortcode('vsb_otr_global', [$this, 'afficherPageGlobalOtr']);
    }

    public function afficherGestionOtr() {
        $otrRepo = new OTRRepository();
        $mailer = new MailerService();
        $message = '';

        $match_id = isset($_GET['id_confrontation']) ? intval($_GET['id_confrontation']) : 0;
        if (!$match_id) return "<p>ID de match manquant.</p>";

        $match = $otrRepo->getInfosMatch($match_id);
        if (!$match) return "<p>Match introuvable.</p>";

        $otrs = $otrRepo->getOtrsParEquipe($match->conf_equ_id);

        // Envoi des demandes
        if (isset($_POST['envoyer_demande']) && !empty($_POST['otr_select'])) {
            foreach ($_POST['otr_select'] as $otr_id) {
                $otr = array_filter($otrs, fn($o) => $o->otr_id == $otr_id)[0];
                $mailer->envoyerMailDispoOTR($otr->pers_mail, $otr->pers_prenom, $match->equ_cat, $match->conf_date, $match->conf_heure, $match->conf_lieu, $match->conf_adversaire);
            }
            $message = '<div class="notice notice-success">Mails envoyés.</div>';
        }

        // Assignation des rôles
        foreach (['chrono', 'secondes', 'tablette'] as $role) {
            if (!empty($_POST["assign_$role"])) {
                $otr_id = intval($_POST["assign_$role"]);
                $otr = array_filter($otrs, fn($o) => $o->otr_id == $otr_id)[0];
                $otrRepo->assignerOTR($match_id, $otr_id, $role);
                $mailer->envoyerMailConfirmationRole($otr->pers_mail, $otr->pers_prenom, $match->equ_cat, $role, $match->conf_date, $match->conf_heure, $match->conf_lieu);
                $message = '<div class="notice notice-success">OTR assigné avec succès.</div>';
            }
        }

        $assignes = $otrRepo->getOTRAssignes($match_id);
        $roles_actuels = array_column($assignes, null, 'osm_role');

        ob_start();
        echo $message;
        echo "<h2>Gestion OTR – Match {$match->equ_cat} vs {$match->conf_adversaire}</h2>";

        echo "<h3>Demande de disponibilité</h3>
        <form method='post'>";
        foreach ($otrs as $otr) {
            echo "<label><input type='checkbox' name='otr_select[]' value='{$otr->otr_id}'> {$otr->pers_prenom} {$otr->pers_nom} ({$otr->pers_mail})</label><br>";
        }
        echo "<p><input type='submit' name='envoyer_demande' value='Envoyer'></p></form>";

        echo "<h3>Assignation des rôles</h3><form method='post'>";
        foreach (['chrono', 'secondes', 'tablette'] as $role) {
            echo "<p><label>$role : 
                <select name='assign_$role'>
                    <option value=''>-- Sélectionner --</option>";
            foreach ($otrs as $otr) {
                echo "<option value='{$otr->otr_id}'>{$otr->pers_prenom} {$otr->pers_nom}</option>";
            }
            echo "</select></label> <input type='submit' value='Assigner'></p>";
        }
        echo "</form>";

        echo "<h3>Rôles assignés</h3><ul>";
        foreach (['chrono', 'secondes', 'tablette'] as $role) {
            if (isset($roles_actuels[$role])) {
                $r = $roles_actuels[$role];
                echo "<li>$role : {$r->pers_prenom} {$r->pers_nom}</li>";
            } else {
                echo "<li>$role : Aucun OTR assigné</li>";
            }
        }
        echo "</ul>";

        return ob_get_clean();
    }

    public function afficherPageGlobalOtr() {
        $otrRepo = new OTRRepository();
        $matchs = $otrRepo->getMatchsAvecMoinsDe2Otr();

        ob_start();
        if (empty($matchs)) {
            echo "<p>Tous les matchs ont assez d'OTR.</p>";
        } else {
            $current_team = '';
            echo "<h2>Matchs avec OTR manquants</h2>";
            foreach ($matchs as $match) {
                if ($match->equ_cat !== $current_team) {
                    if ($current_team !== '') echo "</ul>";
                    $current_team = $match->equ_cat;
                    echo "<h3>Équipe : $current_team</h3><ul>";
                }
                $url = site_url('/gestion-otr/?id_confrontation=' . $match->conf_id);
                echo "<li>{$match->conf_date} à {$match->conf_heure} contre {$match->conf_adversaire} – <a href='$url'>Gérer OTR</a></li>";
            }
            echo "</ul>";
        }
        return ob_get_clean();
    }
}
