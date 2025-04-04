<?php
namespace VSB\controllers;

use VSB\repositories\ComptaRepository;
use VSB\services\ComptaService;
use VSB\services\SaisonService;
use \VSB\services\ComptaStatsService;

class ComptaController {

    public function registerShortcodes() {
        add_shortcode('vsb_compta_equipe', [$this, 'afficherComptaEquipe']);
        add_shortcode('vsb_stats_compta', [$this, 'afficherStatsCompta']);
        add_shortcode('vsb_compta_joueur', [$this, 'afficherComptaJoueur']);

    }

    public function afficherComptaEquipe() {
        $equipe_id = isset($_GET['equipe_id']) ? intval($_GET['equipe_id']) : 0;
        if (!$equipe_id) return "<p>Aucune équipe sélectionnée.</p>";

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

            $message = '<div class="notice notice-success"><p>Comptabilité mise à jour avec succès.</p></div>';
            $comptas = $repo->getComptaParEquipe($equipe_id, $saison_id); // Refresh
        }

        ob_start();
        echo $message;
        echo "<h2>Comptabilité de l'équipe</h2>";

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
        <h2>Statistiques comptabilité – saison en cours</h2>
    
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
        echo "<h2>Historique comptabilité du joueur</h2>";
    
        if (empty($comptas)) {
            echo "<p>Aucune donnée disponible.</p>";
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
    
        return ob_get_clean();
    }
    
}
