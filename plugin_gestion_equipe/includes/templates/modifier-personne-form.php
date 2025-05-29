<?php
if (empty($_GET['pers_id'])) {
    echo "<p>Erreur : Aucune personne sélectionnée.</p>";
    return;
}

$index = $_GET['pers_id'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modifier un joueur</title>

  <!-- POLICES -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">

  <style>
    #modifier_personne {
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

    #modifier_personne label {
      display: inline-block;
      width: 200px;
      text-align: right;
      margin-right: 20px;
      font-size: 16px;
      vertical-align: middle;
      font-family: 'Poppins', sans-serif;
    }

    #modifier_personne input[type="text"],
    #modifier_personne input[type="email"],
    #modifier_personne input[type="number"],
    #modifier_personne input[type="date"],
    #modifier_personne select {
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

    #modifier_personne input[type="checkbox"] {
      transform: scale(1.2);
      vertical-align: middle;
    }

    #modifier_personne button[type="submit"] {
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

    #modifier_personne button[type="submit"]:hover {
      background-color: #69c10f;
      transform: scale(1.03);
    }
  </style>
</head>


<body>
    <h1 class="titre-ajout">Modifier une personne</h1>
    <form id="modifier_personne">
        <?php $hide_title = true; ?>
        <?php include MY_PLUGIN_PATH . '/includes/templates/part-personne-form.php'; ?>
        <button type="submit">Modifier</button>

    </form>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {
            let joueurOriginal = {};
            const $parent1 = $("#parent1");
            const $parent2 = $("#parent2");
            // Récupération des infos du joueur
            let url = "<?php echo esc_url(rest_url('gestion-equipe/v1/personne/' . urlencode($_GET['pers_id']))); ?>";
            $.ajax({
                url: url,
                type: "GET",
                success: function (data) {
                    console.log(data);
                    $('#index').val(data._pers_id);  // Si vous avez un champ hidden #index
                    $('#name').val(data.pers_nom);
                    $('#firstName').val(data.pers_prenom);
                    $('#dateNaissance').val(data.pers_date_nai);
                    $('#sexe').val(data.pers_sexe);
                    $('#noLicence').val(data.pers_num_licence);
                    $('#licenceOk').prop('checked', !!data.pers_licence_ok);  // booléen
                    $('#nationaliteUne').val(data.pers_nationalite_une);
                    $('#nationaliteDeux').val(data.pers_nationalite_deux);
                    $('#address').val(data.pers_adresse);
                    $('#npa').val(data.pers_NPA);
                    $('#noTelephone').val(data.pers_telephone);
                    $('#email').val(data.pers_mail);
                    $('#dateClub').val(data.pers_entree_club);
                },
                error: function (xhr) {
                    console.error("Erreur lors du chargement du joueur :", xhr);
                }
            });

            $('#modifier_personne').submit(function (event) {
                event.preventDefault();

                let formData = new FormData(this);
                console.log(formData);
                $.ajax({
                    type: "POST",
                    url: "<?php echo esc_url(rest_url('gestion-equipe/v1/personne/' . $index)); ?>",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        alert(response.message);
                        window.location.href = "/liste-des-personnes";
                    },
                    error: function (xhr, error) {
                        alert("Erreur lors de la modification de la personne:" + error.responseJSON.message);
                    }
                });
            });
        });
    </script>
</body>

</html>