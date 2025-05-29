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
        if (!$match_id) {
            ob_start();
            echo <<<HTML
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
            <style>
                .vsb-error-message {
                    font-family: 'Poppins', sans-serif;
                    font-size: 16px;
                    color: #d9534f;
                    background-color: #f9d6d5;
                    padding: 10px 15px;
                    border-left: 5px solid #d9534f;
                    border-radius: 5px;
                    margin: 30px auto;
                    max-width: 800px;
                }
            </style>
            <p class='vsb-error-message'>ID de match manquant.</p>
            HTML;
            return ob_get_clean();
        }

        $match = $otrRepo->getInfosMatch($match_id);
        if (!$match) {
            ob_start();
            echo <<<HTML
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
            <style>
                .vsb-error-message {
                    font-family: 'Poppins', sans-serif;
                    font-size: 16px;
                    color: #d9534f;
                    background-color: #f9d6d5;
                    padding: 10px 15px;
                    border-left: 5px solid #d9534f;
                    border-radius: 5px;
                    margin: 30px auto;
                    max-width: 800px;
                }
            </style>
            <p class='vsb-error-message'>Match introuvable.</p>
            HTML;
            return ob_get_clean();
        }

        // Suppression OTR
        if (isset($_GET['remove_role']) && in_array($_GET['remove_role'], ['chrono', 'secondes', 'tablette'])) {
            $otrRepo->supprimerAssignationOTR($match_id, sanitize_text_field($_GET['remove_role']));
            $message = "<div class='vsb-success-message'>Assignation supprimée avec succès.</div>";
        }

        // Charger les OTRs
        $otrs = $otrRepo->getOtrsParEquipe($match->conf_equ_id);
        $otrStats = $otrRepo->getStatsOtrParRole();
        foreach ($otrs as &$otr) {
            $id = $otr->otr_id;
            $stats = $otrStats[$id] ?? ['chrono' => 0, 'secondes' => 0, 'tablette' => 0];
            $otr->libelle_complet = "{$otr->pers_prenom} {$otr->pers_nom} (Chrono: {$stats['chrono']} | Secondes: {$stats['secondes']} | Tablette: {$stats['tablette']})";
        }
        unset($otr);

        // Envoi des mails
        if (isset($_POST['envoyer_demande']) && !empty($_POST['otr_select'])) {
            foreach ($_POST['otr_select'] as $otr_id) {
                $otr_match = array_values(array_filter($otrs, fn($o) => $o->otr_id == $otr_id));
                if (!empty($otr_match)) {
                    $otr = $otr_match[0];
                    $mailer->envoyerMailDispoOTR($otr->pers_mail, $otr->pers_prenom, $match->equ_cat, $match->conf_date, $match->conf_heure, $match->conf_lieu, $match->conf_adversaire);
                } else {
                    error_log("OTR ID introuvable : $otr_id");
                }
            }
            $message = "<div class='vsb-success-message'>Mails envoyés avec succès.</div>";
        }
        /* Assignation des rôles
        foreach (['chrono', 'secondes', 'tablette'] as $role) {
            if (!empty($_POST["assign_$role"])) {
                $otr_id = intval($_POST["assign_$role"]);
                $otr = array_filter($otrs, fn($o) => $o->otr_id == $otr_id)[0];
                $otrRepo->assignerOTR($match_id, $otr_id, $role);
                $mailer->envoyerMailConfirmationRole($otr->pers_mail, $otr->pers_prenom, $match->equ_cat, $role, $match->conf_date, $match->conf_heure, $match->conf_lieu);
                $message = '<div class="notice notice-success">OTR assigné avec succès.</div>';
            }
        }*/

        // Assignation des rôles
        foreach (['chrono', 'secondes', 'tablette'] as $role) {
            if (!empty($_POST["assign_$role"])) {
                $otr_id = intval($_POST["assign_$role"]);
                $otr_match = array_values(array_filter($otrs, fn($o) => $o->otr_id == $otr_id));
                if (!empty($otr_match)) {
                    $otr = $otr_match[0];
                    $otrRepo->assignerOTR($match_id, $otr_id, $role);
                    $mailer->envoyerMailConfirmationRole(
                        $otr->pers_mail,
                        $otr->pers_prenom,
                        $match->equ_cat,
                        $role,
                        $match->conf_date,
                        $match->conf_heure,
                        $match->conf_lieu
                    );
                    $message = "<div class='vsb-success-message'>OTR assigné avec succès.</div>";
                } else {
                    error_log("⚠️ Aucun OTR trouvé avec l'ID $otr_id pour le rôle $role.");
                    $message = "<div class='vsb-error-message'>Erreur : OTR introuvable pour le rôle " . esc_html($role) . ".</div>";
                }
            }
        }

        $assignes = $otrRepo->getOTRAssignes($match_id);
        $roles_actuels = array_column($assignes, null, 'osm_role');

        ob_start();
        echo <<<HTML
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            .vsb-error-message {
                font-family: 'Poppins', sans-serif;
                font-size: 16px;
                color: #d9534f;
                background-color: #f9d6d5;
                padding: 10px 15px;
                border-left: 5px solid #d9534f;
                border-radius: 5px;
                margin: 30px auto;
                max-width: 800px;
            }
            .vsb-success-message {
                font-family: 'Poppins', sans-serif;
                font-size: 16px;
                color: #3c763d;
                background-color: #dff0d8;
                padding: 10px 15px;
                border-left: 5px solid #3c763d;
                border-radius: 5px;
                margin: 30px auto;
                max-width: 800px;
            }
            .otr-container {
                max-width: 960px;
                margin: 0 auto;
                padding: 30px 20px;
                font-family: 'Poppins', sans-serif;
            }
            .otr-container h1 {
                font-family: 'Montserrat', sans-serif;
                font-size: 64px;
                font-style: italic;
                color: #7cda24;
                text-transform: uppercase;
                text-align: center;
                margin-bottom: 40px;
            }
            .otr-container h2 {
                font-size: 22px;
                font-weight: 600;
                margin-top: 30px;
                color: #444;
            }
            .otr-container form {
                margin-bottom: 30px;
            }
            .otr-container label {
                display: block;
                margin-bottom: 8px;
                font-size: 14px;
            }
            .otr-container select,
            .otr-container input[type="submit"],
            .otr-container button {
                font-family: 'Poppins', sans-serif;
                padding: 8px 12px;
                font-size: 14px;
                margin: 5px 0;
                border-radius: 6px;
                border: 1px solid #ccc;
            }
            .otr-container input[type="submit"],
            .otr-container button {
                background-color: #7cda24;
                color: white;
                font-weight: 600;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
            }
            .otr-container input[type="submit"]:hover,
            .otr-container button:hover {
                background-color: #69c10f;
                transform: scale(1.03);
            }
            .otr-container ul {
                padding-left: 20px;
                list-style-type: disc;
            }
            .otr-container li {
                margin-bottom: 8px;
            }
            .otr-container a {
                color: #7cda24;
                font-weight: 500;
                text-decoration: none;
            }
            .otr-container a:hover {
                text-decoration: underline;
            }
        </style>
        <div class="otr-container">
        HTML;

        echo $message;

        echo "<h1>Gestion OTR - Match {$match->equ_cat} vs {$match->conf_adversaire}</h1>";
        echo "<h2>Demande de disponibilité</h2><form method='post'>";
        foreach ($otrs as $otr) {
            echo "<label><input type='checkbox' name='otr_select[]' value='{$otr->otr_id}'> {$otr->libelle_complet} ({$otr->pers_mail})</label><br>";
        }
        echo "<p><input type='submit' name='envoyer_demande' value='Envoyer'></p></form>";

        echo "<h2>Assignation des rôles</h2><form method='post'>";
        foreach (['chrono', 'secondes', 'tablette'] as $role) {
            echo "<p><label>$role : <select name='assign_$role'><option value=''>-- Sélectionner --</option>";
            foreach ($otrs as $otr) {
                echo "<option value='{$otr->otr_id}'>{$otr->libelle_complet}</option>";
            }
            echo "</select></label> <input type='submit' value='Assigner'></p>";
        }
        echo "</form>";

        echo "<h2>Rôles assignés</h2><ul>";
        foreach (['chrono', 'secondes', 'tablette'] as $role) {
            if (isset($roles_actuels[$role])) {
                $r = $roles_actuels[$role];
                $url_supp = add_query_arg([
                    'id_confrontation' => $match_id,
                    'remove_role' => $role
                ], site_url('/gestion-otr/'));
                echo "<li>$role : {$r->pers_prenom} {$r->pers_nom} – <a href='$url_supp' onclick='return confirm(\"Retirer cet OTR ?\")'>Retirer</a></li>";
            } else {
                echo "<li>$role : Aucun OTR assigné</li>";
            }
        }
        echo "</ul></div>";

        return ob_get_clean();
    }

    public function afficherPageGlobalOtr() {
        $otrRepo = new OTRRepository();
        $matchs = $otrRepo->getMatchsAvecOtrPourProchainMois();

        ob_start();
        echo <<<HTML
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            .otr-global-container {
                max-width: 960px;
                margin: 0 auto;
                padding: 30px 20px;
                font-family: 'Poppins', sans-serif;
            }

            .otr-global-container h1 {
                font-family: 'Montserrat', sans-serif;
                font-size: 64px;
                font-style: italic;
                color: #7cda24;
                text-transform: uppercase;
                text-align: center;
                margin-bottom: 40px;
            }

            .otr-global-container h2 {
                font-size: 22px;
                font-weight: 600;
                color: #444;
                margin-top: 30px;
                margin-bottom: 10px;
            }

            .otr-global-container h3 {
                font-size: 18px;
                font-weight: 500;
                color: #555;
                margin-top: 25px;
            }

            .otr-global-container ul {
                list-style-type: disc;
                padding-left: 25px;
                margin-bottom: 20px;
            }

            .otr-global-container li {
                margin-bottom: 8px;
            }

            .otr-global-container a {
                color: #7cda24;
                font-weight: 500;
                text-decoration: none;
            }

            .otr-global-container a:hover {
                text-decoration: underline;
            }

            .vsb-info-message {
                font-family: 'Poppins', sans-serif;
                font-size: 16px;
                color: #31708f;
                background-color: #d9edf7;
                padding: 10px 15px;
                border-left: 5px solid #31708f;
                border-radius: 5px;
                margin: 30px auto;
                max-width: 800px;
            }
        </style>

        <div class="otr-global-container">
            <h1>Disponibilités OTR</h1>
        HTML;

        if (empty($matchs)) {
            echo "<div class='vsb-info-message'>Tous les matchs ont assez d'OTR pour le prochain mois.</div>";
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

        echo "</div>"; // ferme container
        return ob_get_clean();
    }
}
