<?php
namespace VSB\controllers;

use VSB\repositories\ComptaRepository;
use VSB\services\ComptaService;
use VSB\services\ComptaStatsService;
use VSB\services\SaisonService;

class ComptaController {

    public function registerShortcodes() {
        add_shortcode('vsb_compta_equipe', [$this, 'afficherComptaEquipe']);
        add_shortcode('vsb_stats_compta', [$this, 'afficherStatsCompta']);
        add_shortcode('vsb_compta_joueur', [$this, 'afficherComptaJoueur']);

    }

    private function renderMessage(string $type, string $texte): string {
        $css = <<<HTML
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            .vsb-success-message, .vsb-error-message {
                font-family: 'Poppins', sans-serif;
                font-size: 16px;
                padding: 10px 15px;
                border-left: 5px solid;
                border-radius: 5px;
                margin: 30px auto;
                max-width: 800px;
            }
            .vsb-success-message {
                color: #3c763d;
                background-color: #dff0d8;
                border-color: #3c763d;
            }
            .vsb-error-message {
                color: #d9534f;
                background-color: #f9d6d5;
                border-color: #d9534f;
            }
        </style>
        HTML;

        return $css . "<div class='vsb-{$type}-message'>{$texte}</div>";
    }

    public function afficherComptaEquipe() {
        $equipe_id = isset($_GET['equipe_id']) ? intval($_GET['equipe_id']) : 0;
        if (!$equipe_id) return $this->renderMessage('error', 'Aucune équipe spécifiée.');

        $repo = new ComptaRepository();
        $service = new ComptaService();
        $saisonService = new SaisonService();

        $saison_id = $saisonService->getSaisonEnCoursId();
        $comptas = $repo->getComptaParEquipe($equipe_id, $saison_id);

        $message = '';

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pers_id'])) {
            foreach ($_POST['pers_id'] as $index => $pers_id) {
                $data = $service->preparerDonneesCompta($pers_id, $saison_id, [
                    'cotisation' => $_POST['cotisation'][$index] ?? 0,
                    'rabais_cotisation' => $_POST['rabais_cotisation'][$index] ?? 0,
                    'licence' => $_POST['licence'][$index] ?? 0,
                    'rabais_licence' => $_POST['rabais_licence'][$index] ?? 0,
                    'frais_entree' => $_POST['frais_entree'][$index] ?? 0,
                    'paye' => isset($_POST['paye'][$index]) ? 1 : 0
                ]);
                $repo->updateOrInsertCompta($data);
            }

            $message = $this->renderMessage('success', 'Comptabilité mise à jour avec succès.');
            $comptas = $repo->getComptaParEquipe($equipe_id, $saison_id); // Refresh
        }

        ob_start();
        echo '<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">';
        echo '<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">';

        echo $message;
        echo <<<HTML
        <style>
            .vsb-container {
                max-width: 960px;
                margin: 0 auto;
                padding: 20px;
                font-family: 'Poppins', sans-serif;
                
            }

            .vsb-container h1 {
                font-family: 'Montserrat', sans-serif;
                font-size: 108px;
                font-style: italic;
                color: #7cda24;
                text-transform: uppercase;
                text-align: center;
                margin-bottom: 30px;
            }

            .vsb-container table {
                border-collapse: collapse;
                margin: 0 auto 30px auto; /* centrage horizontal */
                font-size: 14px;
                width: auto;
                min-width: 1000px; /* ou une largeur selon tes besoins */
            }

            .vsb-container th,
            .vsb-container td {
                border: 1px solid #ccc;
                padding: 10px;
                text-align: center;
            }

            .vsb-container button,
            .vsb-container input[type="submit"] {
                background-color: #7cda24;
                color: white;
                font-family: 'Poppins', sans-serif;
                font-weight: 600;
                font-size: 15px;
                border: none;
                border-radius: 6px;
                padding: 10px 20px;
                cursor: pointer;
                margin-top: 10px;
            }

            .vsb-container button:hover,
            .vsb-container input[type="submit"]:hover {
                background-color: #69c10f;
                transform: scale(1.02);
            }

            .vsb-container input[type="number"],
            .vsb-container input[type="text"],
            .vsb-container select {
                padding: 6px 10px;
                font-family: 'Poppins', sans-serif;
                font-size: 14px;
                border-radius: 5px;
                border: 1px solid #ccc;
                margin-bottom: 8px;
                height: 36px;
                width: 90px;
                font-size: 14px;
                text-align: center;
            }

            .vsb-container form {
                text-align: center;
            }

            .vsb-table-wrapper { /* Surtout pour le défilement horizontal principalement pour les smartphones */
                overflow-x: auto;
            }

        </style>
        HTML;
        echo "<div class='vsb-container'>";
        echo "<h1>Comptabilité de l'équipe</h1>";
        echo '<div class="vsb-table-wrapper">';
        echo '<form method="post"><table border="1" cellpadding="5" cellspacing="0">';
        echo '<tr>
            <th>Nom</th><th>Prénom</th>
            <th>Cotisation</th><th>Rabais coti</th>
            <th>Licence</th><th>Rabais licence</th>
            <th>Frais entrée</th><th>Total</th><th>Payé</th>
        </tr>';

        foreach ($comptas as $i => $compta) {
            echo "<tr>";
            echo "<td>" . esc_html($compta->pers_nom) . "</td>";
            echo "<td>" . esc_html($compta->pers_prenom) . "</td>";

            echo "<input type='hidden' name='pers_id[]' value='{$compta->pers_id}'>";

            echo "<td><input type='number' step='0.01' name='cotisation[]' value='" . esc_attr($compta->compt_prix_cotisation ?? 0) . "'></td>";
            echo "<td><input type='number' step='0.01' name='rabais_cotisation[]' value='" . esc_attr($compta->compt_rabais_coti ?? 0) . "'></td>";
            echo "<td><input type='number' step='0.01' name='licence[]' value='" . esc_attr($compta->compt_prix_licence ?? 0) . "'></td>";
            echo "<td><input type='number' step='0.01' name='rabais_licence[]' value='" . esc_attr($compta->compt_rabais_licence ?? 0) . "'></td>";
            echo "<td><input type='number' step='0.01' name='frais_entree[]' value='" . esc_attr($compta->compt_frais_dentree ?? 0) . "'></td>";
            echo "<td>" . number_format($compta->compt_prix_tot ?? 0, 2) . "</td>";
            echo "<td><input type='checkbox' name='paye[]' value='1' " . (!empty($compta->compt_paye) ? 'checked' : '') . "></td>";
            echo "</tr>";
        }

        echo "</table><br><button type='submit'>💾 Enregistrer</button></form>";
        echo "</div>"; // Close table wrapper
        echo "</div>"; // Close container
        return ob_get_clean();
    }

    public function afficherStatsCompta() {
        $repo = new ComptaRepository();
        $statsService = new ComptaStatsService();
        $saisonService = new SaisonService();
        $comptaService = new ComptaService();
    
        $saison_id = $saisonService->getSaisonEnCoursId();
        $comptas = $repo->getComptaGlobale($saison_id);
    
        $filtre = $_GET['filtre'] ?? 'tous';
        $search = strtolower($_GET['search'] ?? '');
    
        $comptas = array_filter($comptas, function ($c) use ($filtre, $search) {
            $match = empty($search) || str_contains(strtolower($c->pers_nom . ' ' . $c->pers_prenom), $search);
            if ($filtre === 'payes') return $match && $c->compt_paye;
            if ($filtre === 'non_payes') return $match && !$c->compt_paye;
            return $match;
        });
    
        $stats = $statsService->calculerStatsGlobale($comptas);
    
        ob_start();
        ?>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            .stat-compta-container {
                max-width: 1000px;
                margin: 0 auto;
                padding: 30px 20px;
                font-family: 'Poppins', sans-serif;
            }

            .stat-compta-container h1 {
                font-family: 'Montserrat', sans-serif;
                font-size: 48px;
                font-style: italic;
                text-align: center;
                color: #7cda24;
                margin-bottom: 30px;
                text-transform: uppercase;
            }

            .stat-compta-container form {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                margin-bottom: 20px;
                justify-content: center;
            }

            .stat-compta-container input[type="text"],
            .stat-compta-container select {
                padding: 10px;
                font-size: 14px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-family: 'Poppins', sans-serif;
            }

            .stat-compta-container button {
                background-color: #7cda24;
                color: white;
                font-weight: bold;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
            }

            .stat-compta-container button:hover {
                background-color: #69c10f;
                transform: scale(1.03);
            }

            .stat-compta-container p {
                font-size: 16px;
                margin: 5px 0;
            }

            .stat-compta-container table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                font-size: 14px;
            }

            .stat-compta-container th,
            .stat-compta-container td {
                border: 1px solid #ccc;
                padding: 10px;
                text-align: center;
            }

            .stat-compta-container th {
                background-color: #f0f0f0;
                font-weight: 600;
            }

            .compta-joueur-container {
                max-width: 900px;
                margin: 0 auto;
                padding: 30px 20px;
                font-family: 'Poppins', sans-serif;
            }

            .compta-joueur-container h2 {
                font-family: 'Montserrat', sans-serif;
                font-size: 40px;
                font-style: italic;
                text-align: center;
                color: #7cda24;
                margin-bottom: 20px;
                text-transform: uppercase;
            }

            .compta-joueur-container table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                font-size: 14px;
            }

            .compta-joueur-container th,
            .compta-joueur-container td {
                border: 1px solid #ccc;
                padding: 10px;
                text-align: center;
            }

            .compta-joueur-container th {
                background-color: #f0f0f0;
                font-weight: 600;
            }

            .compta-joueur-container p {
                font-size: 16px;
            }
        </style>
        <div class="stat-compta-container">
            <h1>Statistiques comptabilité – saison en cours</h1>
        
            <form method="get">
                <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>">
                <input type="text" name="search" placeholder="Recherche joueur" value="<?php echo esc_attr($_GET['search'] ?? ''); ?>">
                <select name="filtre">
                    <option value="tous" <?php selected($filtre, 'tous'); ?>>Tous</option>
                    <option value="payes" <?php selected($filtre, 'payes'); ?>>Payés</option>
                    <option value="non_payes" <?php selected($filtre, 'non_payes'); ?>>Non payés</option>
                </select>
                <button type="submit">Filtrer</button>
            </form>
        
            <p><strong>Total encaissé :</strong> <?php echo $stats['total']; ?> CHF</p>
            <p><strong>Total attendu :</strong> <?php echo $stats['attendu']; ?> CHF</p>
            <p><strong>Moyenne par joueur :</strong> <?php echo $stats['moyenne']; ?> CHF</p>
            <p><strong>Payés :</strong> <?php echo $stats['payes']; ?> / <?php echo $stats['payes'] + $stats['non_payes']; ?></p>
        
            <table border="1" cellpadding="4" cellspacing="0">
                <tr><th>Nom</th><th>Prénom</th><th>Total</th><th>Statut</th><th>Actions</th></tr>
                <?php foreach ($comptas as $compta): ?>
                    <tr>
                        <td><?php echo esc_html($compta->pers_nom); ?></td>
                        <td><?php echo esc_html($compta->pers_prenom); ?></td>
                        <td><?php echo number_format($compta->compt_prix_tot, 2); ?> CHF</td>
                        <td><?php echo $comptaService->getBadgePaiement($compta->compt_paye); ?></td>
                        <td><a href="<?php echo site_url('/compta-joueur/?joueur_id=' . $compta->pers_id); ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function afficherComptaJoueur() {
        $repo = new ComptaRepository();
        $comptaService = new ComptaService();
    
        $pers_id = isset($_GET['joueur_id']) ? intval($_GET['joueur_id']) : 0;
        if (!$pers_id) return "<p>Joueur non spécifié.</p>";
    
        $comptas = $repo->getComptaParJoueur($pers_id);
    
        ob_start();
        echo <<<HTML
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            .compta-joueur-container {
                max-width: 900px;
                margin: 0 auto;
                padding: 30px 20px;
                font-family: 'Poppins', sans-serif;
            }

            .compta-joueur-container h2 {
                font-family: 'Montserrat', sans-serif;
                font-size: 40px;
                font-style: italic;
                text-align: center;
                color: #7cda24;
                margin-bottom: 20px;
                text-transform: uppercase;
            }

            .compta-joueur-container table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                font-size: 14px;
            }

            .compta-joueur-container th,
            .compta-joueur-container td {
                border: 1px solid #ccc;
                padding: 10px;
                text-align: center;
            }

            .compta-joueur-container th {
                background-color: #f0f0f0;
                font-weight: 600;
            }

            .compta-joueur-container p {
                font-size: 16px;
            }
        </style>
        <div class="compta-joueur-container">
        <h2>Historique comptabilité du joueur</h2>
        HTML;
    
        if (empty($comptas)) {
            echo $this->renderMessage('error', 'Aucune donnée disponible.');
        } else {
            echo '<table border="1" cellpadding="5" cellspacing="0">';
            echo '<tr><th>Saison</th><th>Total</th><th>Payé</th><th>Licence</th><th>Cotisation</th><th>Frais</th></tr>';
            foreach ($comptas as $c) {
                echo "<tr>";
                echo "<td>" . esc_html("{$c->sai_annee_debut}-{$c->sai_annee_fin}") . "</td>";
                echo "<td>" . number_format($c->compt_prix_tot ?? 0, 2) . " CHF</td>";
                echo "<td>" . $comptaService->getBadgePaiement($c->compt_paye) . "</td>";
                echo "<td>" . number_format($c->compt_prix_licence ?? 0, 2) . "</td>";
                echo "<td>" . number_format($c->compt_prix_cotisation ?? 0, 2) . "</td>";
                echo "<td>" . number_format($c->compt_frais_dentree ?? 0, 2) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        echo "</div>"; // Close container
    
        return ob_get_clean();
    }
    
}
