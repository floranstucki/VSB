<?php

add_shortcode('liste_joueurs', 'afficher_liste_joueurs');

function afficher_liste_joueurs()
{
    // Définir le chemin du fichier JSON
    $file_path = plugin_dir_path(__FILE__) . 'joueur_form.json';

    // Vérifier si le fichier existe
    if (!file_exists($file_path)) {
        return "<p>Aucun joueur enregistré.</p>";
    }

    // Lire et décoder le fichier JSON
    $joueurs = json_decode(file_get_contents($file_path), true);

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
        $html .= "<tr>
            <td>{$joueur['name']}</td>
            <td>{$joueur['firstName']}</td>
            <td>{$joueur['dateNaissance']}</td>
            <td>{$joueur['address']}</td>
            <td>{$joueur['npa']}</td>
            <td>{$joueur['equipe']}</td>
            <td>
                <a href='#' onclick='modifierJoueur({$index})'>Modifier</a>
                <a href='#' onclick='supprimerJoueur({$index})'>Supprimer</a>
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

        function modifierJoueur(index) {
            window.location.href = "/modifier-joueur?index=" + index;
        }

        function supprimerJoueur(index) {
            if (confirm("Voulez-vous vraiment supprimer ce joueur ?")) {
                fetch("/wp-json/gestion-equipe/v1/supprimer-joueur/" + index, {
                    method: "DELETE"
                }).then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                }).catch(error => console.error("Erreur:", error));
            }
        }
    </script>';

    return $html;
}