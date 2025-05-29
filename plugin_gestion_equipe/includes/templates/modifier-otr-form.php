<?php
if (empty($_GET['otr_id'])) {
    echo "<p>Erreur : Aucun OTR sélectionné.</p>";
    return;
}

$index = $_GET['otr_id'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- POLICES -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
  

  <style>
    #modifier_otr {
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

    #modifier_otr label {
      display: inline-block;
      width: 200px;
      text-align: right;
      margin-right: 20px;
      font-size: 16px;
      vertical-align: middle;
      font-family: 'Poppins', sans-serif;
    }

    #modifier_otr input[type="text"],
    #modifier_otr input[type="email"],
    #modifier_otr input[type="number"],
    #modifier_otr input[type="date"],
    #modifier_otr select {
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

    #modifier_otr input[type="checkbox"] {
      transform: scale(1.2);
      vertical-align: middle;
    }

    #modifier_otr button[type="submit"] {
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

    #modifier_otr button[type="submit"]:hover {
      background-color: #69c10f;
      transform: scale(1.03);
    }
  </style>
</head>

<body>
    <h1 class="titre-ajout">Modifier un OTR</h1>
    <form id="modifier_otr">
        <?php wp_nonce_field('wp_rest'); ?>

        <!-- Champ caché pour l'index -->
        <input type="hidden" name="index" id="index">

        <label for="name">OTR : </label>
        <input type="text" name="name" id="name" readonly><br>

        <label for="niveauOTR">Niveau d'OTR :</label>
        <input type="text" name="niveauOTR" id="niveauOTR" required><br>

        <button type="submit">Modifier</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {
            let url = "<?php echo esc_url(rest_url('gestion-equipe/v1/otr/' . urlencode($_GET['otr_id']))); ?>";

            $.ajax({
                url: url,
                type: "GET",
                success: function (data) {

                    console.log(data);
                    $('#index').val(data.otr_pers_id);
                    $('#name').val(data.pers_nom + ' ' + data.pers_prenom);
                    $('#niveauOTR').val(data.otr_niveau_otr);
                },
                error: function (xhr) {
                    console.error("Erreur lors du chargement du joueur :", xhr);
                }
            });
            $('#modifier_otr').submit(function (event) {
                event.preventDefault();

                let formData = new FormData(this);
                console.log(formData);
                $.ajax({
                    type: "POST",
                    url: "<?php echo esc_url(rest_url('gestion-equipe/v1/otr/' . $index)); ?>",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        alert(response.message);
                        window.location.href = "/liste-des-otrs";
                    },
                    error: function (xhr, error) {
                        alert("Erreur lors de la modification de l'OTR:" + error.responseJSON.message);
                    }
                });
            });
        });
    </script>
</body>

</html>