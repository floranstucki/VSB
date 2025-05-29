<?php
if (empty($_GET['jou_id'])) {
    echo "<p>Erreur : Aucun joueur sélectionné.</p>";
    return;
}

$index = $_GET['jou_id'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un joueur</title>
    <!-- POLICES -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        #modifier_joueur {
            max-width: 960px;
            margin: 0 auto;
            padding: 20px;
            display: block;
        }

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

        #modifier_joueur label {
            display: inline-block;
            width: 200px;
            text-align: right;
            margin-right: 20px;
            font-size: 16px;
            vertical-align: middle;
            font-family: 'Poppins', sans-serif;
        }

        #modifier_joueur input[type="text"],
        #modifier_joueur input[type="email"],
        #modifier_joueur input[type="number"],
        #modifier_joueur input[type="date"],
        #modifier_joueur select {
            width: calc(100% - 240px);
            max-width: 500px;
            padding: 8px 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            margin-bottom: 15px;
            vertical-align: middle;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        #modifier_joueur input[type="checkbox"] {
            transform: scale(1.2);
            vertical-align: middle;
        }

        #modifier_joueur button[type="submit"] {
            font-family: 'Poppins', sans-serif;
            background-color: #7cda24;
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #modifier_joueur button[type="submit"]:hover {
            background-color: #69c10f;
            transform: scale(1.03);
        }

        /* Select2 personnalisée */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #7cda24;
            color: white;
        }

        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #7cda24;
            color: white;
        }

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
    <h1 class="titre-ajout">Modifier un joueur</h1>
    <form id="modifier_joueur">
        <?php wp_nonce_field('wp_rest'); ?>
        <input type="hidden" name="index" id="index">

        <label for="name">Joueur :</label>
        <input type="text" name="name" id="name" readonly><br>

        <label for="dateNaissance">Numéro Maillot :</label>
        <input type="number" name="noMaillot" id="noMaillot" required><br>

        <label for="isActif">Actif</label>
        <input type="checkbox" name="isActif" id="isActif"><br>

        <label for="parent1">Parent 1 :</label>
        <select id="parent1" name="parent1" style="width: 300px">
            <option value="">Chargement...</option>
        </select><br>

        <label for="parent2">Parent 2 :</label>
        <select id="parent2" name="parent2" style="width: 300px">
            <option value="">Chargement...</option>
        </select><br>

        <label for="equipe">Équipe</label>
        <select id="equipe" name="equipe" style="width: 300px">
            <option value="">Chargement...</option>
        </select><br>

        <button type="submit">Modifier</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {
            let joueurOriginal = {};
            const $parent1 = $("#parent1");
            const $parent2 = $("#parent2");
            const $equipe = $("#equipe");

            let url = "<?php echo esc_url(rest_url('gestion-equipe/v1/joueur/' . urlencode($_GET['jou_id']))); ?>";
            $.ajax({
                url: url,
                type: "GET",
                success: function (data) {
                    joueurOriginal = {
                        name: data.pers_nom + ' ' + data.pers_prenom,
                        noMaillot: String(data.jou_num_maillot),
                        isActif: data.jou_actif ? "on" : null,
                        parent1: String(data.jou_pere || ""),
                        parent2: String(data.jou_mere || ""),
                        equipe: data.equ_cat
                    };
                    $('#index').val(data.jou_pers_id);
                    $('#name').val(data.pers_nom + ' ' + data.pers_prenom);
                    $('#noMaillot').val(data.jou_num_maillot);
                    $('#isActif').prop('checked', data.jou_actif);
                    chargerSelect($parent1, data.jou_pere);
                    chargerSelect($parent2, data.jou_mere);
                    chargerEquipe($equipe, data.equ_id);
                },
                error: function (xhr) {
                    console.error("Erreur lors du chargement du joueur :", xhr);
                }
            });

            function chargerEquipe($select, selectedValue = null) {
                $select.empty().append('<option value="">Sélectionnez une équipe</option>');
                $.ajax({
                    url: "<?php echo get_rest_url(null, 'gestion-equipe/v1/equipessaison'); ?>",
                    method: "GET",
                    success: function (data) {
                        data.forEach(function (equipe) {
                            $select.append(
                                `<option value="${equipe.equ_id}">${equipe.equ_cat}</option>`
                            );
                        });
                        if (selectedValue) {
                            $select.val(selectedValue);
                        }
                        $select.trigger("change");
                    },
                    error: function (error) {
                        alert("Erreur lors de la récupération des équipes");
                        console.error(error);
                    }
                });
            }

            function chargerSelect($select, selectedValue = null) {
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
                        if (selectedValue) {
                            $select.val(selectedValue);
                        }
                        $select.trigger("change");
                    },
                    error: function (error) {
                        alert("Erreur lors de la récupération des personnes");
                        console.error(error);
                    }
                });
            }

            // ✅ Initialiser Select2 pour les 3 champs
            $parent1.select2({ placeholder: "Sélectionnez un parent" });
            $parent2.select2({ placeholder: "Sélectionnez un parent" });
            $equipe.select2({ placeholder: "Sélectionnez une équipe" });

            $('#modifier_joueur').submit(function (event) {
                event.preventDefault();

                let formData = new FormData(this);
                let formObject = Object.fromEntries(formData.entries());

                const noChangement =
                    formObject.noMaillot === joueurOriginal.noMaillot &&
                    formObject.isActif === joueurOriginal.isActif &&
                    formObject.parent1 === joueurOriginal.parent1 &&
                    formObject.parent2 === joueurOriginal.parent2 &&
                    formObject.equipe === joueurOriginal.equipe;

                if (noChangement) {
                    alert("Aucune modification détectée.");
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "<?php echo esc_url(rest_url('gestion-equipe/v1/joueur/' . $index)); ?>",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        alert(response.message);
                        window.location.href = "/liste-des-joueurs";
                    },
                    error: function (xhr, error) {
                        alert("Erreur lors de la modification du joueur:" + error.responseJSON.message);
                    }
                });
            });
        });
    </script>
</body>

</html>
