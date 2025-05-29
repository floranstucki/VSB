<?php
add_shortcode('liste_joueurs_1', 'afficher_liste_joueurs_1');

function afficher_liste_joueurs_1()
{
    // Vérification de l'équipe en GET
    $equipe_filtre = isset($_GET['equipe']) ? htmlspecialchars($_GET['equipe']) : null;

    $url = '/ajouter-un-joueur/';
    if ($equipe_filtre !== null) {
        $url .= '?equipe=' . urlencode($equipe_filtre);
    }

    ob_start();
    echo <<<HTML
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <style>
        .liste-joueurs-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 20px;
            font-family: 'Poppins', sans-serif;
        }

        .liste-joueurs-container h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 48px;
            font-style: italic;
            text-align: center;
            color: #7cda24;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .liste-joueurs-container a {
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

        .liste-joueurs-container a:hover {
            background-color: #69c10f;
            transform: scale(1.03);
        }

        table#joueursTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        table#joueursTable th,
        table#joueursTable td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        table#joueursTable th {
            background-color: #f0f0f0;
            cursor: pointer;
            font-weight: 600;
        }

        table#joueursTable tr:nth-child(even) {
            background-color: #fafafa;
        }

        table#joueursTable td a {
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

        table#joueursTable td a:hover {
            background-color: #69c10f;
            transform: scale(1.05);
        }

        table#joueursTable td:last-child {
            white-space: nowrap;
        }

        @media screen and (max-width: 768px) {
            table#joueursTable th, table#joueursTable td {
                font-size: 12px;
                padding: 8px;
            }

            .liste-joueurs-container a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
    <div class="liste-joueurs-container">
    <h1>Liste des joueurs</h1>
    HTML;
    ?>
    <a href="<?= esc_url($url); ?>">Ajouter un joueur</a>

    <table border="1" id="joueursTable">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Nom</th>
                <th onclick="sortTable(1)">Prénom</th>
                <th onclick="sortTable(2)">Date de naissance</th>
                <th>Adresse</th>
                <th>NPA</th>
                <th onclick="sortTable(3)">Équipe</th>
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
            let equipe = "<?php echo esc_js($equipe_filtre); ?>";
            console.log(equipe);
            <?php if ($equipe_filtre === NULL): ?>
                let url = "<?php echo esc_url(rest_url('gestion-equipe/v1/joueurs')); ?>";
            <?php else: ?>
                let url = "<?php echo esc_url(rest_url('gestion-equipe/v1/joueurs/' . urlencode($equipe_filtre))); ?>";
            <?php endif; ?>


            $.ajax({
                url: url,
                method: "GET",
                success: function (joueurs) {
                    if (!Array.isArray(joueurs) || joueurs.length === 0) {
                        $('#joueursTable tbody').html("<tr><td colspan='7'>Aucun joueur trouvé.</td></tr>");
                        return;
                    }

                    let rows = "";
                    joueurs.forEach(joueur => {
                        console.log(joueur);
                        rows += `<tr>
                        <td>${joueur.pers_nom}</td>
                        <td>${joueur.pers_prenom}</td>
                        <td>${joueur.pers_date_nai}</td>
                        <td>${joueur.pers_adresse}</td>
                        <td>${joueur.pers_NPA}</td>
                        <td>${joueur.equ_cat}</td>
                        <td>
                            <a href="#" onclick="modifierJoueur(${joueur.jou_id})">Modifier</a>
                            <a href="#" onclick="supprimerJoueur(${joueur.jou_id})">Supprimer</a>
                        </td>
                    </tr>`;
                    });

                    $('#joueursTable tbody').html(rows);
                },
                error: function (xhr) {
                    $('#joueursTable tbody').html("<tr><td colspan='7'>Aucun joueur trouvé !</td></tr>");
                    console.error("Erreur AJAX :", xhr.responseText);
                }
            });
        });

        function sortTable(columnIndex) {
            let table = document.getElementById("joueursTable");
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
            window.location.href = "/modifier-joueur?jou_id=" + id;
        }

        function supprimerJoueur(id) {
            if (!confirm("Es-tu sûr de vouloir supprimer ce joueur ? Cette action est irréversible.")) {
                return; 
            }

            let url = "<?php echo esc_url(rest_url('gestion-equipe/v1/joueur/')); ?>" + encodeURIComponent(id);

            $.ajax({
                url: url,
                method: "DELETE",
                success: function (response) {
                    alert("Joueur supprimé !");
                    // Redirige ou recharge la liste des joueurs
                    window.location.href = "/liste-des-joueurs";
                },
                error: function (xhr) {
                    console.error("Erreur lors de la suppression :", xhr.responseText);
                    alert("Une erreur est survenue pendant la suppression.");
                }
            });
        }


    </script>
    <?php
    echo "</div>"; // Fin de .liste-joueurs-container
    return ob_get_clean();
}
