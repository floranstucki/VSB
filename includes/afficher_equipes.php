<?php


require_once 'fonctions.php';

function afficher_equipe(){

    $equipe = obtenir_joueurs_par_equipes($_GET['equipe']);

    if (!is_array($equipe)) {
        return "<p>Erreur de lecture de l'équipe.</p>";
    }

    
}