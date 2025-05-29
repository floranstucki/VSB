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

    public function afficherStatsMatch() {
        $repo = new StatsRepository();
        $match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;
        $stats = $repo->getStatsParMatch($match_id);

        ob_start();
        echo <<<HTML
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            .stats-match-container {
                max-width: 1000px;
                margin: 0 auto;
                padding: 30px 20px;
                font-family: 'Poppins', sans-serif;
            }

            .stats-match-container h2 {
                font-family: 'Montserrat', sans-serif;
                font-size: 48px;
                font-style: italic;
                text-align: center;
                color: #7cda24;
                margin-bottom: 30px;
                text-transform: uppercase;
            }

            .stats-match-container table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            .stats-match-container th,
            .stats-match-container td {
                border: 1px solid #ccc;
                padding: 10px;
                text-align: center;
                font-size: 14px;
            }

            .stats-match-container th {
                background-color: #f0f0f0;
                font-weight: 600;
            }

            .stats-match-container td a {
                color: #7cda24;
                text-decoration: none;
                font-weight: 500;
            }

            .stats-match-container td a:hover {
                text-decoration: underline;
            }

            .stats-match-container p {
                text-align: center;
                font-size: 16px;
                color: #666;
            }
        </style>
        <div class="stats-match-container">
        HTML;

        echo "<h2>Statistiques du match</h2>";

        if (empty($stats)) {
            echo $this->renderMessage('error', 'Aucune statistique trouvée pour ce match.');
            echo "</div>";
            return ob_get_clean();
        }

        echo "<table><tr><th>Joueur</th><th>Points</th><th>3pts</th><th>2pts</th><th>LF</th><th>Fautes</th><th>Temps</th><th>Éval</th><th>Actions</th></tr>";

        foreach ($stats as $s) {
            $modif_url = site_url('/modifier-stats/') . "?stats_id={$s->stats_id}&match_id=$match_id";
            $saison_url = site_url('/statistiques-saison-du-joueur/') . "?joueur_id={$s->stats_joueur_id}";

            echo "<tr>
                <td>" . esc_html("{$s->pers_prenom} {$s->pers_nom}") . "</td>
                <td>{$s->stats_points}</td>
                <td>{$s->stats_troisPts}</td>
                <td>{$s->stats_deuxPts}</td>
                <td>{$s->stats_lancerMis}/{$s->stats_lancerTot}</td>
                <td>{$s->stats_fautes}</td>
                <td>{$s->stats_tempsDeJeu}</td>
                <td>{$s->stats_eval}</td>
                <td><a href='" . esc_url($modif_url) . "'>Modifier</a> | <a href='" . esc_url($saison_url) . "'>Saison</a></td>
            </tr>";
        }

        echo "</table>";
        echo "</div>";

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
            wp_redirect(site_url('/statistiques-du-match/?match_id=' . $match_id));
            exit;
        }

        $stat = $repo->getStatById($stats_id);
        if (!$stat) return $this->renderMessage('error', 'Stat introuvable.');

        ob_start();
        echo <<<HTML
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            .modifier-stat-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 30px 20px;
                font-family: 'Poppins', sans-serif;
            }

            .modifier-stat-container h1 {
                font-family: 'Montserrat', sans-serif;
                font-size: 48px;
                font-style: italic;
                text-align: center;
                color: #7cda24;
                margin-bottom: 40px;
                text-transform: uppercase;
            }

            .modifier-stat-container form {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .modifier-stat-container label {
                font-weight: 500;
                margin-bottom: 5px;
                display: block;
            }

            .modifier-stat-container input {
                padding: 10px;
                font-size: 16px;
                font-family: 'Poppins', sans-serif;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .modifier-stat-container input[type="submit"] {
                background-color: #7cda24;
                color: white;
                font-weight: bold;
                border: none;
                padding: 12px 30px;
                text-transform: uppercase;
                cursor: pointer;
                align-self: center;
                transition: background-color 0.3s ease, transform 0.2s ease;
            }

            .modifier-stat-container input[type="submit"]:hover {
                background-color: #69c10f;
                transform: scale(1.03);
            }
        </style>
        HTML;

        echo $message;
        echo '<div class="modifier-stat-container">';
        echo '<h1>Modifier les stats</h1>';
        ?>
        <form method="post">
            <p><label>Points :</label> <input type="number" name="points" value="<?php echo esc_attr($stat->stats_points); ?>"></p>
            <p><label>3pts :</label> <input type="number" name="trois" value="<?php echo esc_attr($stat->stats_troisPts); ?>"></p>
            <p><label>2pts :</label> <input type="number" name="deux" value="<?php echo esc_attr($stat->stats_deuxPts); ?>"></p>
            <p><label>LF marqués :</label> <input type="number" name="lf_mis" value="<?php echo esc_attr($stat->stats_lancerMis); ?>"></p>
            <p><label>LF tentés :</label> <input type="number" name="lf_tot" value="<?php echo esc_attr($stat->stats_lancerTot); ?>"></p>
            <p><label>Fautes :</label> <input type="number" name="fautes" value="<?php echo esc_attr($stat->stats_fautes); ?>"></p>
            <p><label>Temps de jeu :</label> <input type="number" step="0.1" name="temps" value="<?php echo esc_attr($stat->stats_tempsDeJeu); ?>"></p>
            <p><label>Évaluation :</label> <input type="number" name="eval" value="<?php echo esc_attr($stat->stats_eval); ?>"></p>
            <p><input type="submit" name="vsb_modif_stat" value="Enregistrer"></p>
        </form>
        <?php
        echo '</div>';
        return ob_get_clean();
    }


    public function afficherStatsJoueurSaison() {
        $repo = new StatsRepository();
        $saisonService = new SaisonService();
        $joueur_id = isset($_GET['joueur_id']) ? intval($_GET['joueur_id']) : 0;
        $saison_id = $saisonService->getIdSaisonEnCours();
        $stats = $repo->getStatsJoueurSaison($joueur_id, $saison_id);

        ob_start();
        echo <<<HTML
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            .stats-joueur-container {
                max-width: 1000px;
                margin: 0 auto;
                padding: 30px 20px;
                font-family: 'Poppins', sans-serif;
            }

            .stats-joueur-container h2 {
                font-family: 'Montserrat', sans-serif;
                font-size: 48px;
                font-style: italic;
                text-align: center;
                color: #7cda24;
                margin-bottom: 20px;
                text-transform: uppercase;
            }

            .stats-joueur-container table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            .stats-joueur-container th,
            .stats-joueur-container td {
                border: 1px solid #ccc;
                padding: 10px;
                text-align: center;
                font-size: 14px;
            }

            .stats-joueur-container th {
                background-color: #f0f0f0;
                font-weight: 600;
            }

            .stats-joueur-container h3 {
                margin-top: 30px;
                font-size: 22px;
                font-weight: 600;
                color: #444;
            }

            .stats-joueur-container ul {
                padding-left: 20px;
                list-style: disc;
            }

            .stats-joueur-container li {
                margin-bottom: 5px;
            }

            .stats-joueur-container p {
                font-size: 16px;
                margin-bottom: 10px;
            }
        </style>
        <div class="stats-joueur-container">
        HTML;

        echo "<h2>Stats du joueur – Saison " . esc_html($saisonService->getSaisonLabelEnCours()) . "</h2>";
        echo "<p>Matchs joués cette saison :</p>";

        if (!$stats) {
            echo $this->renderMessage('error', 'Aucune statistique disponible pour ce joueur cette saison.');
            echo "</div>";
            return ob_get_clean();
        }


        $total = ['pts' => 0, '3' => 0, '2' => 0, 'lf' => 0, 'lf_t' => 0, 'f' => 0, 'tps' => 0, 'eval' => 0];

        echo "<table><tr><th>Date</th><th>Adversaire</th><th>Points</th><th>3pts</th><th>2pts</th><th>LF</th><th>Fautes</th><th>Temps</th><th>Éval</th></tr>";
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
                <td>" . esc_html($s->conf_date) . "</td>
                <td>" . esc_html($s->conf_adversaire) . "</td>
                <td>{$s->stats_points}</td>
                <td>{$s->stats_troisPts}</td>
                <td>{$s->stats_deuxPts}</td>
                <td>{$s->stats_lancerMis}/{$s->stats_lancerTot}</td>
                <td>{$s->stats_fautes}</td>
                <td>{$s->stats_tempsDeJeu}</td>
                <td>{$s->stats_eval}</td>
            </tr>";
        }
        echo "</table>";

        $count = count($stats);
        echo "<h3>Moyennes</h3><ul>";
        echo "<li>Points : " . round($total['pts'] / $count, 1) . "</li>";
        echo "<li>3pts : " . round($total['3'] / $count, 1) . "</li>";
        echo "<li>2pts : " . round($total['2'] / $count, 1) . "</li>";
        echo "<li>LF : " . round($total['lf'] / $count, 1) . " / " . round($total['lf_t'] / $count, 1) . "</li>";
        echo "<li>Fautes : " . round($total['f'] / $count, 1) . "</li>";
        echo "<li>Temps de jeu : " . round($total['tps'] / $count, 1) . " min</li>";
        echo "<li>Évaluation : " . round($total['eval'] / $count, 1) . "</li>";
        echo "</ul>";
        echo "</div>";

        return ob_get_clean();
    }
}
