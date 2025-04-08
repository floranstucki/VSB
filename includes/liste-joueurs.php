<?php

add_shortcode('liste_joueurs_1', 'afficher_liste_joueurs_1');
require_once 'fonctions.php';
//require_once './fonctions.php';

//fonction pour afficher les joueurs 
function afficher_liste_joueurs_1()
{
    // Définir le chemin du fichier JSON
    $file_path = plugin_dir_path(__FILE__) . 'joueur_form.json';

    // Vérifier si le fichier existe
    if (!file_exists($file_path)) {
        return "<p>Aucun joueur enregistré.</p>";
    }

    // Lire et décoder le fichier JSON
    $joueurs = obtenir_joueurs();

    if (!is_array($joueurs)) {
        return "<p>Erreur de lecture des joueurs.</p>";
    }

    // Vérification de l'équipe en GET (pour filtrer les joueurs)
    $equipe_filtre = isset($_GET['equipe']) ? htmlspecialchars($_GET['equipe']) : null;

    // Filtrer les joueurs en fonction de l'équipe (si un filtre est appliqué)
    $joueurs_filtres = array_filter($joueurs, function ($joueur) use ($equipe_filtre) {
        return !$equipe_filtre || $joueur['equipe'] === $equipe_filtre;
    });

    $url = '/ajouter-joueur/';
    if ($equipe_filtre !== null) {
        $url .= '?equipe=' . urlencode($equipe_filtre);
    }

    $html = '<a href="' . $url . '">Ajouter un joueur</a>';
    
    if (empty($joueurs_filtres)) {
        return $html . "<br><p>Aucun joueur trouvé.</p>";
    }

    // Générer le tableau avec les informations des joueurs filtrés
    $html .= "<table border='1' id='joueursTable'>
        <thead>
            <tr>
                <th onclick='sortTable(0)'>Nom</th>
                <th onclick='sortTable(1)'>Prénom</th>
                <th onclick='sortTable(2)'>Date de naissance</th>
                <th>Adresse</th>
                <th>NPA</th>
                <th onclick='sortTable(3)'>Équipe</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>";

    foreach ($joueurs_filtres as $index => $joueur) {
        $nom = isset($joueur['pers_nom']) ? $joueur['pers_nom'] : 'N/A';
        $prenom = isset($joueur['pers_prenom']) ? $joueur['pers_prenom'] : 'N/A';
        $date_nai = isset($joueur['pers_date_nai']) ? $joueur['pers_date_nai'] : 'N/A';
        $adresse = isset($joueur['pers_adresse']) ? $joueur['pers_adresse'] : 'N/A';
        $npa = isset($joueur['pers_NPA']) ? $joueur['pers_NPA'] : 'N/A';
        $equipe = isset($joueur['equ_cat']) ? $joueur['equ_cat'] : 'N/A';
    
        $html .= "<tr>
            <td>$nom</td>
            <td>$prenom</td>
            <td>$date_nai</td>
            <td>$adresse</td>
            <td>$npa</td>
            <td>$equipe</td>
            <td>
                <a href='#' onclick='modifierJoueur({$joueur["jou_id"]})'>Modifier</a>
                <a href='#' onclick='supprimerJoueur({$joueur["jou_id"]})'>Supprimer</a>
            </td>
        </tr>";
    }

    $html .= "</tbody></table>";

    // Ajouter le script JavaScript pour gérer le tri et les actions
    $html .= '<script>
        let sortOrder = {};

        function sortTable(columnIndex) {
            let table = document.getElementById("joueursTable");
            let rows = Array.from(table.getElementsByTagName("tr")).slice(1);
            let asc = sortOrder[columnIndex] === "asc" ? false : true;
            
            rows.sort((a, b) => {
                let cellA = a.getElementsByTagName("td")[columnIndex].innerText.toLowerCase();
                let cellB = b.getElementsByTagName("td")[columnIndex].innerText.toLowerCase();
                return asc ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            });

            sortOrder[columnIndex] = asc ? "asc" : "desc";

            let tbody = table.getElementsByTagName("tbody")[0];
            tbody.innerHTML = "";
            rows.forEach(row => tbody.appendChild(row));
        }

        function modifierJoueur(id) {
            window.location.href = "/modifier-joueur?jou_id=" + id;
        }

        function supprimerJoueur(id) {
            if (isNaN(id) || id <= 0) {
                alert("ID invalide.");
                return;
            }

            if (confirm("Voulez-vous vraiment supprimer ce joueur ?")) {
                fetch(`/wp-json/gestion-equipe/v1/supprimer-joueur/${id}`, {
                    method: "DELETE"
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => console.error("Erreur:", error));
            }
}

    </script>';

    return $html;
}