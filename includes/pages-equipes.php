<?php

add_shortcode('liste_equipes', 'afficher_liste_equipes');

function afficher_liste_equipes() {
    $equipes_file = plugin_dir_path(__FILE__) . 'equipe_form.json';
    $joueurs_file = plugin_dir_path(__FILE__) . 'joueur_form.json';

    // Vérifier l'existence des fichiers
    if (!file_exists($equipes_file)) {
        return "<p>Aucune équipe enregistrée.</p>";
    }

    // Charger les équipes
    $json_equipes = file_get_contents($equipes_file);
    $equipes = json_decode($json_equipes, true);

    if (!is_array($equipes)) {
        return "<p>Erreur de lecture des équipes.</p>";
    }

    // Charger les joueurs si le fichier existe
    $joueurs = [];
    if (file_exists($joueurs_file)) {
        $json_joueurs = file_get_contents($joueurs_file);
        $joueurs_data = json_decode($json_joueurs, true);

        if (is_array($joueurs_data)) {
            // Compter le nombre de joueurs par équipe
            foreach ($joueurs_data as $joueur) {
                if (isset($joueur['equipe'])) {
                    $equipe_nom = $joueur['equipe'];
                    if (!isset($joueurs[$equipe_nom])) {
                        $joueurs[$equipe_nom] = 0;
                    }
                    $joueurs[$equipe_nom]++;
            
                 }
            }
            
        }
    }

    $html = "<table border='1'>";
$html .= "<tr><th>Équipe</th><th>Nombre de joueurs</th></tr>";

foreach ($equipes as $equipe) {
    if (isset($equipe['name'])) {
        $nom_equipe = $equipe['name'];
        $nb_joueurs = isset($joueurs[$nom_equipe]) ? $joueurs[$nom_equipe] : 0;
        
        // Ajouter le lien au nom de l'équipe
        $nom_equipe_link = '<a href="/liste-des-joueurs/?equipe=' . urlencode($nom_equipe) . '">' . htmlspecialchars($nom_equipe) . '</a>';

        $html .= "<tr><td>{$nom_equipe_link}</td><td>{$nb_joueurs}</td></tr>";
    }
}
$html .= "</table>";

    
    return $html;
    
}
