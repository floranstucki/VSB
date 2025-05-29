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

    public function afficherFormulaireCreation() {
        $matchRepo = new MatchRepository();
        $saisonService = new SaisonService();
        global $wpdb;
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vsb_confrontation_nonce']) && wp_verify_nonce($_POST['vsb_confrontation_nonce'], 'vsb_creer_confrontation')) {
            $data = [
                'conf_equ_id'        => intval($_POST['id']),
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
                $message = $this->renderMessage('success', 'Confrontation créée avec succès.');
            } else {
                $message = $this->renderMessage('error', 'Erreur lors de la création du match.');
            }
        }

        $equipes = $saisonService->getEquipesSaisonEnCours();

        ob_start();
        echo '<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">';
        echo '<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">';
        echo <<<HTML
        <style>
            .formulaire-match-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                font-family: 'Poppins', sans-serif;
            }

            .formulaire-match-container h1 {
                font-family: 'Montserrat', sans-serif;
                font-size: 64px;
                color: #7cda24;
                font-style: italic;
                text-align: center;
                text-transform: uppercase;
                margin-bottom: 40px;
            }

            .formulaire-match-container form {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .formulaire-match-container label {
                font-weight: 500;
                margin-bottom: 5px;
                display: block;
            }

            .formulaire-match-container input,
            .formulaire-match-container select {
                padding: 10px;
                font-size: 16px;
                font-family: 'Poppins', sans-serif;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .formulaire-match-container input[type="submit"] {
                background-color: #7cda24;
                color: white;
                font-weight: bold;
                text-transform: uppercase;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
                border: none;
                width: fit-content;
                padding: 12px 30px;
                align-self: center;
            }

            .formulaire-match-container input[type="submit"]:hover {
                background-color: #69c10f;
                transform: scale(1.03);
            }

            .formulaire-match-container p {
                margin: 0;
            }
        </style>
        HTML;
        echo $message;
        echo '<div class="formulaire-match-container">';
        ?>
        <h1>Créer un match</h1>
        <form method="post">
            <?php wp_nonce_field('vsb_creer_confrontation', 'vsb_confrontation_nonce'); ?>
            <p>
                <label for="id">Équipe :</label>
                <select name="id" id="id" required>
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
        echo <<<HTML
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            .liste-equipes-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 30px 20px;
                font-family: 'Poppins', sans-serif;
            }

            .liste-equipes-container h2 {
                font-family: 'Montserrat', sans-serif;
                font-size: 48px;
                font-style: italic;
                color: #7cda24;
                text-align: center;
                margin-bottom: 30px;
                text-transform: uppercase;
            }

            .liste-equipes-container ul {
                list-style: none;
                padding: 0;
            }

            .liste-equipes-container li {
                margin-bottom: 15px;
                font-size: 16px;
                background: #f9f9f9;
                border: 1px solid #ccc;
                border-radius: 6px;
                padding: 15px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 20px;
            }

            .liste-equipes-container li span {
                font-weight: 500;
            }

            .liste-equipes-container a {
                color: #7cda24;
                text-decoration: none;
                font-weight: 500;
            }

            .liste-equipes-container a:hover {
                text-decoration: underline;
            }

            .liste-equipes-links {
                display: flex;
                gap: 10px;
            }
        </style>

        <div class="liste-equipes-container">
            <h2>Liste des équipes</h2>
            <ul>
        HTML;

        foreach ($equipes as $equipe) {
            $url_matchs = add_query_arg('equipe_id', $equipe['equ_id'], site_url('/matchs-de-lequipe/'));
            $url_compta = add_query_arg('equipe_id', $equipe['equ_id'], site_url('/compta-dune-equipe/'));

            echo "<li>
                    <span>" . esc_html($equipe['equ_cat']) . "</span>
                    <div class='liste-equipes-links'>
                        <a href='" . esc_url($url_matchs) . "'>Matchs</a>
                        <a href='" . esc_url($url_compta) . "'>Voir Compta</a>
                    </div>
                </li>";
        }

        echo "</ul></div>";

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

            $message = $this->renderMessage('success', 'Score mis à jour !');
        }

        $message = '';
    
        if (!$equipe_id) {
            return $this->renderMessage('error', 'Aucune équipe sélectionnée.');
        }
    
        // Suppression
        if (isset($_GET['supprimer_match'])) {
            $repo->supprimerMatchEtStats(intval($_GET['supprimer_match']));
            $message = $this->renderMessage('success', 'Match supprimé');
        }
    
        $equipe_label = $repo->getEquipeLabel($equipe_id);
        $matchs = $repo->getMatchsByEquipe($equipe_id);
    
        ob_start();
        echo $message;
        echo <<<HTML
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
            <style>
                .matchs-equipe-container {
                    max-width: 960px;
                    margin: 0 auto;
                    padding: 30px 20px;
                    font-family: 'Poppins', sans-serif;
                }

                .matchs-equipe-container h3 {
                    font-family: 'Montserrat', sans-serif;
                    font-size: 48px;
                    color: #7cda24;
                    font-style: italic;
                    text-align: center;
                    text-transform: uppercase;
                    margin-bottom: 30px;
                }

                .matchs-equipe-container table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 30px;
                }

                .matchs-equipe-container th,
                .matchs-equipe-container td {
                    border: 1px solid #ddd;
                    padding: 10px;
                    text-align: center;
                    font-size: 14px;
                }

                .matchs-equipe-container th {
                    background-color: #f3f3f3;
                    font-weight: bold;
                }

                .matchs-equipe-container a {
                    color: #7cda24;
                    text-decoration: none;
                    font-weight: 500;
                }

                .matchs-equipe-container a:hover {
                    text-decoration: underline;
                }

                .matchs-equipe-container p {
                    text-align: center;
                    font-size: 16px;
                    color: #666;
                }
            </style>
            <div class="matchs-equipe-container">
            HTML;
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
            $modif_url = site_url('/modifier-un-match/?match_id=' . $id . '&equipe_id=' . $equipe_id);
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
        <!-- MODAL SCORE - STYLE PERSONNALISÉ -->
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            #vsb-score-modal {
                display: none;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: #ffffff;
                border-radius: 10px;
                padding: 30px 25px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
                z-index: 9999;
                width: 90%;
                max-width: 400px;
                font-family: 'Poppins', sans-serif;
            }

            #vsb-score-modal h3 {
                font-family: 'Montserrat', sans-serif;
                font-size: 26px;
                color: #7cda24;
                text-align: center;
                margin-bottom: 20px;
            }

            #vsb-score-modal form {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            #vsb-score-modal label {
                font-weight: 500;
                font-size: 14px;
                margin-bottom: 5px;
            }

            #vsb-score-modal input[type="number"] {
                padding: 10px;
                font-size: 16px;
                font-family: 'Poppins', sans-serif;
                border: 1px solid #ccc;
                border-radius: 5px;
                text-align: center;
            }

            #vsb-score-modal button {
                background-color: #7cda24;
                color: white;
                font-weight: bold;
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
                font-size: 14px;
            }

            #vsb-score-modal button:hover {
                background-color: #69c10f;
                transform: scale(1.03);
            }

            .vsb-score-btns {
                display: flex;
                justify-content: space-between;
                gap: 10px;
            }
        </style>
        <div id="vsb-score-modal">
            <h3>Entrer le score</h3>
            <form method="post">
                <input type="hidden" name="vsb_score_match_id" id="vsb_score_match_id">
                <p>
                    <label>Score équipe :</label>
                    <input type="number" name="vsb_score_equipe" required>
                </p>
                <p>
                    <label>Score adverse :</label>
                    <input type="number" name="vsb_score_adverse" required>
                </p>
                <div class="vsb-score-btns">
                    <button type="submit">Valider</button>
                    <button type="button" onclick="vsbCloseModal()">Annuler</button>
                </div>
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
        echo "</div>";
    }
    

    public function modifierMatch() {
        $matchRepo = new MatchRepository();
        $match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;
        $equipe_id = isset($_GET['equipe_id']) ? intval($_GET['equipe_id']) : 0;
        $message = '';

        if (!$match_id) return $this->renderMessage('error', 'Match non spécifié.');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vsb_modifier_match_nonce']) && wp_verify_nonce($_POST['vsb_modifier_match_nonce'], 'vsb_modifier_match')) {
            $data = [
                'conf_date'       => sanitize_text_field($_POST['conf_date']),
                'conf_lieu'       => sanitize_text_field($_POST['conf_lieu']),
                'conf_adversaire' => sanitize_text_field($_POST['conf_adversaire']),
            ];
            $matchRepo->modifierMatch($match_id, $data);

            if ($equipe_id) {
                wp_redirect(site_url('/matchs-de-lequipe/?equipe_id=' . $equipe_id));
                exit;
            }

            $message = $this->renderMessage('success', 'Match modifié avec succès.');
        }


        $match = $matchRepo->getMatchById($match_id);
        if (!$match) return $this->renderMessage('error', 'Match introuvable.');

        ob_start();
        echo $message;
        ?>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            .modifier-match-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 30px 20px;
                font-family: 'Poppins', sans-serif;
            }
            .modifier-match-container h1 {
                font-family: 'Montserrat', sans-serif;
                font-size: 64px;
                font-style: italic;
                color: #7cda24;
                text-transform: uppercase;
                text-align: center;
                margin-bottom: 40px;
            }
            .modifier-match-container form {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }
            .modifier-match-container label {
                font-weight: 500;
                margin-bottom: 5px;
                display: block;
            }
            .modifier-match-container input {
                padding: 10px;
                font-size: 16px;
                font-family: 'Poppins', sans-serif;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
            .modifier-match-container input[type="submit"] {
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
            .modifier-match-container input[type="submit"]:hover {
                background-color: #69c10f;
                transform: scale(1.03);
            }
        </style>
        <div class="modifier-match-container">
            <h1>Modifier un match</h1>
            <form method="post">
                <?php wp_nonce_field('vsb_modifier_match', 'vsb_modifier_match_nonce'); ?>
                <p><label for="conf_date">Date :</label>
                <input type="date" name="conf_date" id="conf_date" value="<?php echo esc_attr($match['conf_date']); ?>" required></p>
                <p><label for="conf_lieu">Lieu :</label>
                <input type="text" name="conf_lieu" id="conf_lieu" value="<?php echo esc_attr($match['conf_lieu']); ?>" required></p>
                <p><label for="conf_adversaire">Adversaire :</label>
                <input type="text" name="conf_adversaire" id="conf_adversaire" value="<?php echo esc_attr($match['conf_adversaire']); ?>" required></p>
                <p><input type="submit" value="Enregistrer les modifications"></p>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
