<?php
namespace VSB\controllers;

use VSB\repositories\MatchRepository;
use VSB\services\SaisonService;



class MatchController {

    public function registerShortcodes() {
        add_shortcode('vsb_form_confrontation', [$this, 'afficherFormulaireCreation']);
        add_shortcode('vsb_liste_equipes_matchs', [$this, 'afficherListeEquipes']);
        add_shortcode('vsb_liste_matchs_equipe', [$this, 'afficherMatchsEquipe']);
        add_shortcode('vsb_modifier_match', [$this, 'modifierMatch']);
    }

    public function afficherFormulaireCreation() {
        $matchRepo = new MatchRepository();
        $saisonService = new SaisonService();
        global $wpdb;
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vsb_confrontation_nonce']) && wp_verify_nonce($_POST['vsb_confrontation_nonce'], 'vsb_creer_confrontation')) {
            $data = [
                'conf_equ_id'        => intval($_POST['equ_id']),
                'conf_date'          => sanitize_text_field($_POST['conf_date']),
                'conf_heure'         => sanitize_text_field($_POST['conf_heure']),
                'conf_lieu'          => sanitize_text_field($_POST['conf_lieu']),
                'conf_adversaire'    => sanitize_text_field($_POST['conf_adversaire']),
                'conf_score_equipe'  => null,
                'conf_score_adverse' => null,
            ];

            $match_id = $matchRepo->creerConfrontation($data);

            if ($match_id) {
                $joueurs = $matchRepo->getJoueursDeLEquipe($data['conf_equ_id']);
                foreach ($joueurs as $joueur) {
                    $wpdb->insert('vsb_statsjoueur', [
                        'stats_match_id'   => $match_id,
                        'stats_joueur_id'  => $joueur->jou_id,
                        'stats_points'     => 0,
                        'stats_troisPts'   => 0,
                        'stats_deuxPts'    => 0,
                        'stats_lancerMis'  => 0,
                        'stats_lancerTot'  => 0,
                        'stats_fautes'     => 0,
                        'stats_tempsDeJeu' => 0,
                        'stats_eval'       => 0
                    ]);
                }
                $message = '<div class="notice notice-success">Confrontation créée avec succès.</div>';
            } else {
                $message = '<div class="notice notice-error">Erreur lors de la création du match.</div>';
            }
        }

        $equipes = $saisonService->getEquipesSaisonEnCours();

        ob_start();
        echo $message;
        ?>
        <form method="post">
            <?php wp_nonce_field('vsb_creer_confrontation', 'vsb_confrontation_nonce'); ?>
            <p>
                <label for="equ_id">Équipe :</label>
                <select name="equ_id" id="equ_id" required>
                    <option value="">Choisissez une équipe</option>
                    <?php foreach ($equipes as $equipe): ?>
                        <option value="<?php echo esc_attr($equipe['equ_id']); ?>">
                            <?php echo esc_html($equipe['equ_cat']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><label>Date :</label> <input type="date" name="conf_date" required></p>
            <p><label>Heure :</label> <input type="time" name="conf_heure" required></p>
            <p><label>Lieu :</label> <input type="text" name="conf_lieu" required></p>
            <p><label>Adversaire :</label> <input type="text" name="conf_adversaire" required></p>
            <p><input type="submit" value="Créer le match"></p>
        </form>
        <?php
        return ob_get_clean();
    }

    public function afficherListeEquipes() {
        $saisonService = new SaisonService();
        $equipes = $saisonService->getEquipesSaisonEnCours();
    
        ob_start();
        echo "<h2>Liste des équipes</h2><ul>";
    
        foreach ($equipes as $equipe) {
            $url_matchs = add_query_arg('equipe_id', $equipe['equ_id'], site_url('/matchs-de-lequipe/'));
            $url_compta = add_query_arg('equipe_id', $equipe['equ_id'], site_url('/compta-dune-equipe/'));
    
            echo "<li>";
            echo esc_html($equipe['equ_cat']) . " — ";
            echo "<a href='{$url_matchs}'>Matchs</a> | ";
            echo "<a href='{$url_compta}'>Voir Compta</a>";
            echo "</li>";
        }
    
        echo "</ul>";
        return ob_get_clean();
    }
    

    public function afficherMatchsEquipe() {
        $repo = new MatchRepository();
        $equipe_id = isset($_GET['equipe_id']) ? intval($_GET['equipe_id']) : 0;
        // Enregistrement du score
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vsb_score_match_id'])) {
            $match_id = intval($_POST['vsb_score_match_id']);
            $score_eq = intval($_POST['vsb_score_equipe']);
            $score_adv = intval($_POST['vsb_score_adverse']);

            $repo->modifierMatch($match_id, [
                'conf_score_equipe' => $score_eq,
                'conf_score_adverse' => $score_adv
            ]);

            $message = "<div class='notice notice-success'>Score mis à jour !</div>";
        }

        $message = '';
    
        if (!$equipe_id) {
            return "<p>Aucune équipe sélectionnée.</p>";
        }
    
        // Suppression
        if (isset($_GET['supprimer_match'])) {
            $repo->supprimerMatchEtStats(intval($_GET['supprimer_match']));
            $message = "<div class='notice notice-success'>Match supprimé.</div>";
        }
    
        $equipe_label = $repo->getEquipeLabel($equipe_id);
        $matchs = $repo->getMatchsByEquipe($equipe_id);
    
        ob_start();
        echo $message;
        echo "<h3>Matchs de l'équipe : $equipe_label</h3>";
    
        if (empty($matchs)) {
            echo "<p>Aucun match trouvé.</p>";
            return ob_get_clean();
        }
    
        echo "<table border='1' cellpadding='5'><tr>
            <th>Date</th><th>Heure</th><th>Adversaire</th><th>Lieu</th><th>Score</th><th>Actions</th>
        </tr>";
    
        foreach ($matchs as $match) {
            $id = $match['conf_id'];
            $modif_url = site_url('/modifier-un-match/?match_id=' . $id);
            $stats_url = site_url('/statistiques-du-match/?match_id=' . $id);
            $otr_url   = site_url('/gestion-otr/?id_confrontation=' . $id);
    
            echo "<tr>
                <td>{$match['conf_date']}</td>
                <td>{$match['conf_heure']}</td>
                <td>{$match['conf_adversaire']}</td>
                <td>{$match['conf_lieu']}</td>
                <td>{$match['conf_score_equipe']} - {$match['conf_score_adverse']}</td>
                <td>
                    <a href='?equipe_id=$equipe_id&supprimer_match=$id' onclick='return confirm(\"Supprimer ce match ?\")'>Supprimer</a> |
                    <a href='$modif_url'>Modifier</a> |
                    <a href='$otr_url'> OTR</a> |
                    <a href='#' onclick='vsbOpenScoreModal($id)'>Score</a> |
                    <a href='$stats_url'>📊 Stats</a>
                </td>
            </tr>";
        }
    
        echo "</table>";
    
        // Modal pour entrer le score
        ?>
        <div id="vsb-score-modal" style="display:none; position:fixed; top:20%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border:1px solid #ccc; z-index:9999;">
            <h3>Entrer le score</h3>
            <form method="post">
                <input type="hidden" name="vsb_score_match_id" id="vsb_score_match_id">
                <p><label>Score équipe :</label><input type="number" name="vsb_score_equipe" required></p>
                <p><label>Score adverse :</label><input type="number" name="vsb_score_adverse" required></p>
                <p>
                    <button type="submit">Valider</button>
                    <button type="button" onclick="vsbCloseModal()">Annuler</button>
                </p>
            </form>
        </div>
    
        <script>
        function vsbOpenScoreModal(matchId) {
            document.getElementById('vsb_score_match_id').value = matchId;
            document.getElementById('vsb-score-modal').style.display = 'block';
        }
        function vsbCloseModal() {
            document.getElementById('vsb-score-modal').style.display = 'none';
        }
        </script>
    
        <?php
        return ob_get_clean();
    }
    

    public function modifierMatch() {
        $matchRepo = new MatchRepository();
        $match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;
        $message = '';

        if (!$match_id) return '<p>Match non spécifié.</p>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vsb_modifier_match_nonce']) && wp_verify_nonce($_POST['vsb_modifier_match_nonce'], 'vsb_modifier_match')) {
            $data = [
                'conf_date'       => sanitize_text_field($_POST['conf_date']),
                'conf_lieu'       => sanitize_text_field($_POST['conf_lieu']),
                'conf_adversaire' => sanitize_text_field($_POST['conf_adversaire']),
            ];
            $matchRepo->modifierMatch($match_id, $data);
            $message = '<div class="notice notice-success">Match modifié avec succès.</div>';
        }

        $match = $matchRepo->getMatchById($match_id);
        if (!$match) return '<p>Match introuvable.</p>';

        ob_start();
        echo $message;
        ?>
        <form method="post">
            <?php wp_nonce_field('vsb_modifier_match', 'vsb_modifier_match_nonce'); ?>
            <p>Date : <input type="date" name="conf_date" value="<?php echo esc_attr($match['conf_date']); ?>"></p>
            <p>Lieu : <input type="text" name="conf_lieu" value="<?php echo esc_attr($match['conf_lieu']); ?>"></p>
            <p>Adversaire : <input type="text" name="conf_adversaire" value="<?php echo esc_attr($match['conf_adversaire']); ?>"></p>
            <p><input type="submit" value="Enregistrer les modifications"></p>
        </form>
        <?php
        return ob_get_clean();
    }
}
