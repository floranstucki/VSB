<?php
add_shortcode('liste_otr', 'afficher_liste_otr');

function afficher_liste_otr()
{
    // Vérification de l'équipe en GET

    $url = '/ajouter-un-otr/';


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

        table#OTRTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        table#OTRTable th,
        table#OTRTable td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        table#OTRTable th {
            background-color: #f0f0f0;
            cursor: pointer;
            font-weight: 600;
        }

        table#OTRTable tr:nth-child(even) {
            background-color: #fafafa;
        }

        table#OTRTable td a {
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

        table#OTRTable td a:hover {
            background-color: #69c10f;
            transform: scale(1.05);
        }

        table#OTRTable td:last-child {
            white-space: nowrap;
        }

        @media screen and (max-width: 768px) {
            table#OTRTable th, table#OTRTable td {
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
    <h1>Liste des OTRS</h1>
    HTML;
    ?>
    <a href="<?= esc_url($url); ?>">Ajouter un OTR</a>

    <table border="1" id="OTRTable">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Nom de l'OTR</th>
                <th onclick="sortTable(1)">Fonction OTR</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3">Chargement...</td>
            </tr>
        </tbody>
    </table>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {

            let url = "<?php echo esc_url(rest_url('gestion-equipe/v1/otrs/')); ?>";



            $.ajax({
                url: url,
                method: "GET",
                success: function (otrs) {
                    if (!Array.isArray(otrs) || otrs.length === 0) {
                        $('#OTRTable tbody').html("<tr><td colspan='3'>Aucun OTR trouvé.</td></tr>");
                        return;
                    }

                    let rows = "";
                    otrs.forEach(otr => {
                        console.log(otr);
                        rows += `<tr>
                        <td>${otr.pers_nom} ${otr.pers_prenom}</td>
                        <td>${otr.otr_niveau_otr}</td>
                        <td>
                            <a href="#" onclick="modifierOtr(${otr.otr_id})">Modifier</a>
                            <a href="#" onclick="supprimerOtr(${otr.otr_id})">Supprimer</a>
                        </td>
                    </tr>`;
                    });

                    $('#OTRTable tbody').html(rows);
                },
                error: function (xhr) {
                    $('#OTRTable tbody').html("<tr><td colspan='3'>Aucun OTR trouvé !</td></tr>");
                    console.error("Erreur AJAX :", xhr.responseText);
                }
            });
        });

        function sortTable(columnIndex) {
            let table = document.getElementById("OTRTable");
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

        function modifierOtr(id) {
            window.location.href = "/modifier-un-otr?otr_id=" + id;
        }

        function supprimerOtr(id) {
            if (!confirm("Es-tu sûr de vouloir supprimer cet OTR ? Cette action est irréversible.")) {
                return;
            }

            let url = "<?php echo esc_url(rest_url('gestion-equipe/v1/otr/')); ?>" + encodeURIComponent(id);

            $.ajax({
                url: url,
                method: "DELETE",
                success: function (response) {
                    alert("OTR supprimé !");
                    // Redirige ou recharge la liste des joueurs
                    window.location.href = "/liste-des-otrs";
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
