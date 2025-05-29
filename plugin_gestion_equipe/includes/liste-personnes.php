<?php
add_shortcode('liste_personnes', 'afficher_liste_personnes');

function afficher_liste_personnes()
{
    // Vérification de l'équipe en GET
    $personne_filtre = isset($_GET['personne']) ? htmlspecialchars($_GET['personne']) : null;

    $url = '/ajouter-une-personne/';
    if ($personne_filtre !== null) {
        $url .= '?personne=' . urlencode($personne_filtre);
    }

    ob_start();
    echo <<<HTML
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <style>
        .liste-personnes-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 20px;
            font-family: 'Poppins', sans-serif;
        }

        .liste-personnes-container h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 48px;
            font-style: italic;
            text-align: center;
            color: #7cda24;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .liste-personnes-container a {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #7cda24;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
            text-transform: uppercase;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .liste-personnes-container a:hover {
            background-color: #69c10f;
            transform: scale(1.03);
        }

        table#personneTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        table#personneTable th,
        table#personneTable td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        table#personneTable th {
            background-color: #f0f0f0;
            cursor: pointer;
            font-weight: 600;
        }

        table#personneTable tr:nth-child(even) {
            background-color: #fafafa;
        }

        table#personneTable td a {
            margin: 0 5px;
            padding: 6px 12px;
            background-color: #7cda24;
            color: white;
            text-decoration: none;
            font-weight: 500;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block;
        }

        table#personneTable td a:hover {
            background-color: #69c10f;
            transform: scale(1.05);
        }

        table#personneTable td:last-child {
            white-space: nowrap;
        }

        @media screen and (max-width: 768px) {
            table#personneTable th, table#personneTable td {
                font-size: 12px;
                padding: 8px;
            }

            .liste-personnes-container a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
    <div class="liste-personnes-container">
    <h1>Liste des personnes</h1>
    HTML;
    ?>
    <a href="<?= esc_url($url); ?>">Ajouter une Personne</a>

    <table border="1" id="personneTable">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Nom</th>
                <th onclick="sortTable(1)">Prénom</th>
                <th onclick="sortTable(2)">Date de naissance</th>
                <th>Adresse</th>
                <th>NPA</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="7">Chargement...</td>
            </tr>
        </tbody>
    </table>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {
            let equipe = "<?php echo esc_js($personne_filtre); ?>";
            console.log(equipe);
            <?php if ($personne_filtre === NULL): ?>
                let url = "<?php echo esc_url(rest_url('gestion-equipe/v1/personnes')); ?>";
            <?php else: ?>
                let url = "<?php echo esc_url(rest_url('gestion-equipe/v1/personne/' . urlencode($$personne_filtre))); ?>";
            <?php endif; ?>


            $.ajax({
                url: url,
                method: "GET",
                success: function (personnes) {
                    if (!Array.isArray(personnes) || personnes.length === 0) {
                        $('#personneTable tbody').html("<tr><td colspan='7'>Aucune personne trouvée.</td></tr>");
                        return;
                    }

                    let rows = "";
                    personnes.forEach(personne => {
                        console.log(personne);
                        rows += `<tr>
                        <td>${personne.pers_nom}</td>
                        <td>${personne.pers_prenom}</td>
                        <td>${personne.pers_date_nai}</td>
                        <td>${personne.pers_adresse}</td>
                        <td>${personne.pers_NPA}</td>
                        <td>
                            <a href="#" onclick="modifierJoueur(${personne.pers_id})">Modifier</a>
                            <a href="#" onclick="supprimerJoueur(${personne.pers_id})">Supprimer</a>
                        </td>
                    </tr>`;
                    });

                    $('#personneTable tbody').html(rows);
                },
                error: function (xhr) {
                    $('#personneTable tbody').html("<tr><td colspan='7'>Aucune personne trouvée !</td></tr>");
                    console.error("Erreur AJAX :", xhr.responseText);
                }
            });
        });

        function sortTable(columnIndex) {
            let table = document.getElementById("personneTable");
            let rows = Array.from(table.getElementsByTagName("tr")).slice(1);
            let asc = table.getAttribute("data-sort-" + columnIndex) !== "asc";
            table.setAttribute("data-sort-" + columnIndex, asc ? "asc" : "desc");

            rows.sort((a, b) => {
                let cellA = a.getElementsByTagName("td")[columnIndex].innerText.toLowerCase();
                let cellB = b.getElementsByTagName("td")[columnIndex].innerText.toLowerCase();
                return asc ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            });

            let tbody = table.getElementsByTagName("tbody")[0];
            tbody.innerHTML = "";
            rows.forEach(row => tbody.appendChild(row));
        }

        function modifierJoueur(id) {
            window.location.href = "/modifier-une-personne?pers_id=" + id;
        }

        function supprimerJoueur(id) {
            if (!confirm("Es-tu sûr de vouloir supprimer cette personne ? Cette action est irréversible.")) {
                return; 
            }

            let url = "<?php echo esc_url(rest_url('gestion-equipe/v1/personne/')); ?>" + encodeURIComponent(id);

            $.ajax({
                url: url,
                method: "DELETE",
                success: function (response) {
                    alert("Personne supprimée !");
                    // Redirige ou recharge la liste des joueurs
                    window.location.href = "/liste-des-personnes";
                },
                error: function (xhr) {
                    console.error("Erreur lors de la suppression :", xhr.responseText);
                    alert("Une erreur est survenue pendant la suppression.");
                }
            });
        }


    </script>
    <?php
    echo "</div>"; // Ferme le conteneur principal
    return ob_get_clean();
}
