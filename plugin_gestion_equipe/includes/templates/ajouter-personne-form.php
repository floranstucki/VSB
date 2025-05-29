<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'une personne</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Conteneur du formulaire */
        #ajout_personne {
            max-width: 960px;
            margin: 0 auto;
            padding: 20px;
            display: block;
        }

        /* Titre principal */
        h1.titre-ajout {
            font-family: 'Montserrat', sans-serif;
            font-size: 108px;
            color: #7cda24;
            font-style: italic;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 40px;
        }

        #ajout_personne,
        #ajout_personne label,
        #ajout_personne input,
        #ajout_personne select,
        #ajout_personne textarea,
        #ajout_personne legend {
            font-family: 'Poppins', sans-serif;
        }

        #ajout_personne label {
            display: inline-block;
            width: 200px;
            /* largeur fixe pour les labels */
            text-align: right;
            margin-right: 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            vertical-align: middle;
        }

        #ajout_personne input[type="text"],
        #ajout_personne input[type="date"],
        #ajout_personne input[type="email"],
        #ajout_personne input[type="number"],
        #ajout_personne select {
            width: calc(100% - 240px);
            /* adapte selon la largeur du label + margin */
            max-width: 500px;
            padding: 8px 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            margin-bottom: 15px;
            vertical-align: middle;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        #ajout_personne input[type="checkbox"] {
            width: auto;
            margin-left: 0;
        }



        /* Aligne chaque ligne label+input sur une même ligne */
        #ajout_personne label,
        #ajout_personne input,
        #ajout_personne select {
            display: inline-block;
        }


        #ajout_personne button[type="submit"] {
            font-family: 'Poppins', sans-serif;
            background-color: #7cda24;
            /* Vert du club */
            color: white;
            font-size: 18px;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            display: block;
            margin: 30px auto 0;
            /* centre le bouton */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #ajout_personne button[type="submit"]:hover {
            background-color: #69c10f;
            /* Vert plus foncé au hover */
            transform: scale(1.03);
        }



        /* Conteneur de la sélection du type */
        /* Centrer toute la zone de sélection */
        legend {
            font-weight: bold;
            font-size: 18px;
            display: block;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>

</head>


<body>
    <form id="ajout_personne">
        <?php wp_nonce_field('wp_rest'); ?>
        <?php include MY_PLUGIN_PATH . '/includes/templates/part-personne-form.php'; ?><br>
        <button type="submit">Ajouter</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {


            // Soumettre le formulaire
            $('#ajout_personne').submit(function (event) {
                event.preventDefault();
                var form = $(this);
                console.log(form.serialize());
                $.ajax({
                    method: "POST",
                    url: "<?php echo esc_url(rest_url('gestion-equipe/v1/addpersonne')); ?>",
                    data: form.serialize(),
                    success: function (response) {
                        alert("Personne ajoutée avec succès !");
                        form.trigger("reset");
                        window.location.href = "/liste-des-personnes";
                    },
                    error: function (error) {
                        alert("Erreur lors de la création de la personne : " + error.responseJSON.message);
                        console.error(error);
                    }
                })
            });
        });

    </script>
</body>

</html>