<?php
add_shortcode('liste_equipes', 'afficher_liste_equipes_ajax');

function afficher_liste_equipes_ajax()
{
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

        .liste-equipes-container h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 48px;
            font-style: italic;
            color: #7cda24;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        #table-equipes {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        #table-equipes th,
        #table-equipes td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
            font-size: 14px;
        }

        #table-equipes th {
            background-color: #f0f0f0;
            font-weight: 600;
        }

        #table-equipes td a {
            color: #7cda24;
            font-weight: 500;
            text-decoration: none;
        }

        #table-equipes td a:hover {
            text-decoration: underline;
        }
    </style>
    <div class="liste-equipes-container">
        <h1>Liste des équipes</h1>
    HTML;
    ?>
    <table id="table-equipes" border="1">
        <thead>
            <tr><th>Équipe</th><th>Nombre de joueurs</th></tr>
        </thead>
        <tbody>
            <tr><td colspan="2">Chargement...</td></tr>
        </tbody>
    </table>

    <script>
    jQuery(document).ready(function($) {
        $.ajax({
            url: "<?php echo esc_url(rest_url('gestion-equipe/v1/equipes')); ?>",
            method: "GET",
            dataType: "json",
            success: function(data) {
                var tbody = $("#table-equipes tbody");
                tbody.empty(); // vider le "Chargement..."

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function(equipe) {
                        var nomEquipe = equipe.equ_cat || "Inconnu";
                        var nbJoueurs = equipe.nombre_joueurs || 0;

                        var lien = '<a href="/liste-des-joueurs/?equipe=' + encodeURIComponent(nomEquipe) + '">' + $('<div>').text(nomEquipe).html() + '</a>';
                        var ligne = "<tr><td>" + lien + "</td><td>" + nbJoueurs + "</td></tr>";
                        tbody.append(ligne);
                    });
                } else {
                    tbody.append("<tr><td colspan='2'>Aucune équipe trouvée.</td></tr>");
                }
            },
            error: function() {
                $("#table-equipes tbody").html("<tr><td colspan='2'>Erreur de chargement des données.</td></tr>");
            }
        });
    });
    </script>
    <?php
    echo "</div>";
    return ob_get_clean();
}
