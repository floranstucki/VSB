<?php
require_once 'fonctions.php';
add_shortcode('liste_equipes', 'afficher_liste_equipes');



function afficher_liste_equipes() {
    // Charger les équipes
    $equipes = obtenir_equipes();

    if (!is_array($equipes)) {
        return "<p>Erreur de lecture des équipes.</p>";
    }

    // Charger les joueurs si le fichier existe
    $joueurs = [];
        $joueurs_data = obtenir_joueurs();

        if (is_array($joueurs_data)) {
            // Compter le nombre de joueurs par équipe
            foreach ($joueurs_data as $joueur) {
                if (isset($joueur['equ_cat'])) {
                    $equipe_nom = $joueur['equ_cat'];
                    if (!isset($joueurs[$equipe_nom])) {
                        $joueurs[$equipe_nom] = 0;
                    }
                    $joueurs[$equipe_nom]++;
            
                 }
            }
            
        }
    

    $html = "<table border='1'>";
$html .= "<tr><th>Équipe</th><th>Nombre de joueurs</th></tr>";

foreach ($equipes as $equipe) {
    if (isset($equipe['equ_cat'])) {
        $nom_equipe = $equipe['equ_cat'];
        $nb_joueurs = isset($joueurs[$nom_equipe]) ? $joueurs[$nom_equipe] : 0;
        
        // Ajouter le lien au nom de l'équipe
        $nom_equipe_link = '<a href="/liste-des-joueurs/?equipe=' . urlencode($nom_equipe) . '">' . htmlspecialchars($nom_equipe) . '</a>';

        $html .= "<tr><td>{$nom_equipe_link}</td><td>{$nb_joueurs}</td></tr>";
    }
}
$html .= "</table>";

    
    return $html;
    
}
