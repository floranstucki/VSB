<?php
namespace VSB\controllers;

use VSB\repositories\StatsRepository;
use VSB\services\SaisonService;

class StatsController {

    public function registerShortcodes() {
        add_shortcode('vsb_stats_match', [$this, 'afficherStatsMatch']);
        add_shortcode('vsb_modifier_stats', [$this, 'modifierStatJoueur']);
        add_shortcode('vsb_stats_joueur', [$this, 'afficherStatsJoueurSaison']);
    }

    public function afficherStatsMatch() {
        $repo = new StatsRepository();
        $match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;
        $stats = $repo->getStatsParMatch($match_id);

        ob_start();
        echo "<h2>Statistiques du match</h2>";
        if (empty($stats)) {
            echo "<p>Aucune statistique trouvée.</p>";
        } else {
            echo "<table border='1'><tr><th>Joueur</th><th>Points</th><th>3pts</th><th>2pts</th><th>LF</th><th>Fautes</th><th>Temps</th><th>Éval</th><th>Actions</th></tr>";
            foreach ($stats as $s) {
                $modif_url = site_url('//modifier-stats/') . "?stats_id={$s->stats_id}&match_id=$match_id";
                echo "<tr>
                    <td>{$s->pers_prenom} {$s->pers_nom}</td>
                    <td>{$s->stats_points}</td>
                    <td>{$s->stats_troisPts}</td>
                    <td>{$s->stats_deuxPts}</td>
                    <td>{$s->stats_lancerMis}/{$s->stats_lancerTot}</td>
                    <td>{$s->stats_fautes}</td>
                    <td>{$s->stats_tempsDeJeu}</td>
                    <td>{$s->stats_eval}</td>
                    <td><a href='" . esc_url($modif_url) . "'>Modifier</a> | <a href='" . esc_url(site_url('//statistiques-saison-du-joueur/') . "?joueur_id={$s->stats_joueur_id}") . "'>Saison</a></td>
                </tr>";
            }
            echo "</table>";
        }
        return ob_get_clean();
    }

    public function modifierStatJoueur() {
        $repo = new StatsRepository();
        $stats_id = isset($_GET['stats_id']) ? intval($_GET['stats_id']) : 0;
        $match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vsb_modif_stat'])) {
            $data = [
                'stats_points'     => intval($_POST['points']),
                'stats_troisPts'   => intval($_POST['trois']),
                'stats_deuxPts'    => intval($_POST['deux']),
                'stats_lancerMis'  => intval($_POST['lf_mis']),
                'stats_lancerTot'  => intval($_POST['lf_tot']),
                'stats_fautes'     => intval($_POST['fautes']),
                'stats_tempsDeJeu' => floatval($_POST['temps']),
                'stats_eval'       => intval($_POST['eval']),
            ];
            $repo->updateStat($stats_id, $data);
            $message = "<div class='notice notice-success'>Statistiques mises à jour !</div>";
        }

        $stat = $repo->getStatById($stats_id);
        if (!$stat) return "<p>Stat introuvable.</p>";

        ob_start();
        echo $message;
        ?>
        <h3>Modifier stats du joueur</h3>
        <form method="post">
            <p>Points : <input type="number" name="points" value="<?php echo $stat->stats_points ?>"></p>
            <p>3pts : <input type="number" name="trois" value="<?php echo $stat->stats_troisPts ?>"></p>
            <p>2pts : <input type="number" name="deux" value="<?php echo $stat->stats_deuxPts ?>"></p>
            <p>LF marqués : <input type="number" name="lf_mis" value="<?php echo $stat->stats_lancerMis ?>"></p>
            <p>LF tentés : <input type="number" name="lf_tot" value="<?php echo $stat->stats_lancerTot ?>"></p>
            <p>Fautes : <input type="number" name="fautes" value="<?php echo $stat->stats_fautes ?>"></p>
            <p>Temps de jeu : <input type="number" step="0.1" name="temps" value="<?php echo $stat->stats_tempsDeJeu ?>"></p>
            <p>Évaluation : <input type="number" name="eval" value="<?php echo $stat->stats_eval ?>"></p>
            <p><input type="submit" name="vsb_modif_stat" value="Enregistrer"></p>
        </form>
        <?php
        return ob_get_clean();
    }

    public function afficherStatsJoueurSaison() {
        $repo = new StatsRepository();
        $saisonService = new SaisonService();
        $joueur_id = isset($_GET['joueur_id']) ? intval($_GET['joueur_id']) : 0;
        $saison_id = $saisonService->getIdSaisonEnCours();
        $stats = $repo->getStatsJoueurSaison($joueur_id, $saison_id);

        ob_start();
        echo "<h2>Stats du joueur – Saison " . $saisonService->getSaisonLabelEnCours() . "</h2>";
        echo "<p>Matchs joués cette saison :</p>";

        if (!$stats) {
            echo "<p>Aucune stat disponible.</p>";
            return ob_get_clean();
        }

        $total = ['pts' => 0, '3' => 0, '2' => 0, 'lf' => 0, 'lf_t' => 0, 'f' => 0, 'tps' => 0, 'eval' => 0];
        echo "<table border='1'><tr><th>Date</th><th>Adversaire</th><th>Points</th><th>3pts</th><th>2pts</th><th>LF</th><th>Fautes</th><th>Temps</th><th>Éval</th></tr>";
        foreach ($stats as $s) {
            $total['pts'] += $s->stats_points;
            $total['3'] += $s->stats_troisPts;
            $total['2'] += $s->stats_deuxPts;
            $total['lf'] += $s->stats_lancerMis;
            $total['lf_t'] += $s->stats_lancerTot;
            $total['f'] += $s->stats_fautes;
            $total['tps'] += $s->stats_tempsDeJeu;
            $total['eval'] += $s->stats_eval;

            echo "<tr>
                <td>{$s->conf_date}</td>
                <td>{$s->conf_adversaire}</td>
                <td>{$s->stats_points}</td>
                <td>{$s->stats_troisPts}</td>
                <td>{$s->stats_deuxPts}</td>
                <td>{$s->stats_lancerMis}/{$s->stats_lancerTot}</td>
                <td>{$s->stats_fautes}</td>
                <td>{$s->stats_tempsDeJeu}</td>
                <td>{$s->stats_eval}</td>
            </tr>";
        }
        $count = count($stats);
        echo "</table><h3>Moyennes</h3><ul>";
        echo "<li>Points : " . round($total['pts'] / $count, 1) . "</li>";
        echo "<li>3pts : " . round($total['3'] / $count, 1) . "</li>";
        echo "<li>2pts : " . round($total['2'] / $count, 1) . "</li>";
        echo "<li>LF : " . round($total['lf'] / $count, 1) . " / " . round($total['lf_t'] / $count, 1) . "</li>";
        echo "<li>Fautes : " . round($total['f'] / $count, 1) . "</li>";
        echo "<li>Temps de jeu : " . round($total['tps'] / $count, 1) . " min</li>";
        echo "<li>Évaluation : " . round($total['eval'] / $count, 1) . "</li></ul>";

        return ob_get_clean();
    }
}
