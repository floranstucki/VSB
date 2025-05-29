<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'un joueur </title>
    <!-- Styles Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">



    <!-- jQuery + Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        #ajout_joueur {
            max-width: 960px;
            margin: 0 auto;
            padding: 20px;
            display: block;
                
        }
        .titre-ajout-joueur {
            font-family: 'Montserrat', sans-serif;
            font-size: 108px;
            color: #7cda24;
            font-style: italic;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 40px;
        }

        #ajout_joueur,
        #ajout_joueur label,
        #ajout_joueur input,
        #ajout_joueur select,
        #ajout_joueur textarea,
        #ajout_joueur legend {
            font-family: 'Poppins', sans-serif;
        }

        #ajout_joueur label {
            display: inline-block;
            width: 200px; /* largeur fixe pour les labels */
            text-align: right;
            margin-right: 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            vertical-align: middle;
        }

        #ajout_joueur input[type="text"],
        #ajout_joueur input[type="email"],
        #ajout_joueur input[type="number"],
        #ajout_joueur input[type="date"],
        #ajout_joueur input[type="checkbox"],
        #ajout_joueur input[type="tel"],
        #ajout_joueur select {
            width: calc(100% - 240px); /* adapte selon la largeur du label + margin */
            max-width: 500px;
            padding: 8px 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            margin-bottom: 15px;
            vertical-align: middle;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        #ajout_joueur input[type="checkbox"] {
            width: auto;
            margin-left: 0;
        }

        #ajout_joueur input#noMaillot {
            width: 100px;
            vertical-align: middle;
        }



        /* Aligne chaque ligne label+input sur une même ligne */
        #ajout_joueur label,
        #ajout_joueur input,
        #ajout_joueur select {
            display: inline-block;
        }


        #ajout_joueur button[type="submit"] {
            font-family: 'Poppins', sans-serif;
            background-color: #7cda24;        /* Vert du club */
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
            margin: 30px auto 0; /* centre le bouton */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #ajout_joueur button[type="submit"]:hover {
            background-color: #69c10f;       /* Vert plus foncé au hover */
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

        .radio-options {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .radio-options label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .radio-options input[type="radio"] {
            margin: 0;
        }
        #new-personne-fields {
            display: none;
        }

        /* === Personnalisation Select2 (menu déroulant vert) === */

        /* Arrière-plan général du menu déroulant */
        .select2-container--default .select2-results > .select2-results__options {
            background-color: white;
            color: #333;
        }

        /* Élément survolé (hover) */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #7cda24; /* Vert du club */
            color: white;
        }

        /* Élément sélectionné */
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #7cda24; /* Vert du club */
            color: white;
        }

        /* Bordure du champ select2 actif */
        .select2-container--default .select2-selection--single {
            border: 1px solid #ccc;
            border-radius: 5px;
            height: 38px;
            padding: 0 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            line-height: 38px !important; /* Alignement vertical du texte */
            display: flex;
            align-items: center; /* Optionnel, en complément */
        }

        .select2-container {
            margin-bottom: 15px !important;
        }

    </style>
    
</head>

<body>
    <h1 class="titre-ajout-joueur">Ajouter un joueur</h1>
    <legend>Type d'ajout :</legend>
    <div class="radio-options">
        <label>
            <input type="radio" id="old" name="form_otr" value="old" checked />
            Personne existante
        </label>
        <label>
            <input type="radio" id="new" name="form_otr" value="new" />
            Nouvelle personne
        </label>
        
    </div>





    <form id="ajout_joueur">
        <?php wp_nonce_field('wp_rest'); ?>

        <!-- Partie sélection d'une personne existante -->
        <div id="select-personne">
            <label for="personne">Personne existante :</label>
            <select id="personne" name="personne" style="width: 300px" required>
                <option value="">Chargement...</option>
            </select>
        </div>

        <div id="new-personne-fields">
            <?php $hide_title = true; ?>
            <?php include MY_PLUGIN_PATH . '/includes/templates/part-personne-form.php'; ?>
        </div>

        <label>Numéro de maillot</label>
        <input type="number" id="noMaillot" name="noMaillot" required><br>

        <label>Joueur actif</label>
        <input type="hidden" name="isActif" value="off">
        <input type="checkbox" id="isActif" name="isActif" value="on"><br>

        <label for="parent1">Parent 1 :</label>
        <select id="parent1" name="parent1" style="width: 300px">
            <option value="">Chargement...</option>
        </select><br>

        <label for="parent2">Parent 2 :</label>
        <select id="parent2" name="parent2" style="width: 300px">
            <option value="">Chargement...</option>
        </select><br>

        <label for="equipe">Équipe :</label>
        <select id="equipe" name="equipe" style="width: 300px" required>
            <option value="">Chargement...</option>
        </select><br>

        <button type="submit">Ajouter</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        jQuery(document).ready(function ($) {
            const $form = $("#ajout_joueur");
            const $selectPersonne = $("#personne");
            const $selectPersonneBlock = $("#select-personne");
            const $newPersonneFields = $("#new-personne-fields");
            const $nom = $("#nom");
            const $prenom = $("#prenom");
            const $parent1 = $("#parent1");
            const $parent2 = $("#parent2");
            const $equipe = $("#equipe");

            function chargerParentDansSelect($select) {
                $select.empty().append('<option value="">Sélectionnez une personne</option>');

                $.ajax({
                    url: "<?php echo get_rest_url(null, 'gestion-equipe/v1/personnes'); ?>",
                    method: "GET",
                    success: function (data) {
                        data.forEach(function (personne) {
                            $select.append(
                                `<option value="${personne.pers_id}">${personne.pers_nom} ${personne.pers_prenom}</option>`
                            );

                        });
                        $select.trigger("change");
                    },
                    error: function (error) {
                        alert("Erreur lors de la récupération des personnes");
                        console.error(error);
                    }
                });

            }

            function chargerSelect($select) {
                $select.empty().append('<option value="">Sélectionnez une personne</option>');

                $.ajax({
                    url: "<?php echo get_rest_url(null, 'gestion-equipe/v1/personnesjoueurs'); ?>",
                    method: "GET",
                    success: function (data) {
                        data.forEach(function (personne) {
                            console.log(personne);
                            $select.append(
                                `<option value="${personne.pers_id}">${personne.pers_nom} ${personne.pers_prenom}</option>`
                            );
                        });
                        $select.trigger("change");
                    },
                    error: function (error) {
                        alert("Erreur lors de la récupération des personnes");
                        console.error(error);
                    }
                });

            }

            function chargerEquipe($select) {
                $select.empty().append('<option value="">Sélectionnez une équipe</option>');

                $.ajax({
                    url: "<?php echo get_rest_url(null, 'gestion-equipe/v1/equipessaison'); ?>",
                    method: "GET",
                    success: function (data) {
                        data.forEach(function (equipe) {
                            console.log(equipe);
                            $select.append(
                                `<option value="${equipe.equ_id}">${equipe.equ_cat}</option>`
                            );
                        });
                        $select.trigger("change");
                    },
                    error: function (error) {
                        alert("Erreur lors de la récupération des équipes");
                        console.error(error);
                    }
                });

            }


            // Appliquer Select2
            chargerSelect($selectPersonne);
            chargerParentDansSelect($parent1);
            chargerParentDansSelect($parent2);
            chargerEquipe($equipe);

            $selectPersonne.select2({ placeholder: "Sélectionnez une personne" });
            $parent1.select2({ placeholder: "Sélectionnez un parent" });
            $parent2.select2({ placeholder: "Sélectionnez un parent" });
            $equipe.select2({ placeholder: "Sélectionnez une équipe" });


            // Gérer le type d'ajout
            $('input[name="form_otr"]').change(function () {
                const isNew = $(this).val() === "new";
                toggleFormType(isNew);
            });

            function toggleFormType(isNew) {
                $newPersonneFields.toggle(isNew);
                $selectPersonneBlock.toggle(!isNew);
                $selectPersonne.prop("required", !isNew);
                $nom.add($prenom).prop("required", isNew);
            }

            // Soumission du formulaire
            $form.on("submit", function (event) {
                event.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: "<?php echo esc_url(get_rest_url(null, 'gestion-equipe/v1/addjoueur')); ?>",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json", // Ajout ici
                    success: function (response) {
                        alert("Joueur ajouté avec succès !");
                        console.log(response);
                        window.location.href = "/liste-des-joueurs";
                    },
                    error: function (xhr, status, error) {
                        alert("Erreur lors de la création du joueur : " + error.responseJSON.message);
                    }
                });
            });
        });
    </script>

</body>

</html>