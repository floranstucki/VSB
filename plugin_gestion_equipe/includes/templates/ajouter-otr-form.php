<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ajout d'un OTR</title>

  <!-- POLICES -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Poppins:wght@400;500&display=swap" rel="stylesheet">

  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <style>
  /* Conteneur principal */
  .ajout-container {
    max-width: 960px;
    margin: 0 auto;
    padding: 20px;
    display: block;
  }

  /* Titre principal */
  .ajout-container h1 {
    font-family: 'Montserrat', sans-serif;
    font-size: 108px;
    color: #7cda24;
    font-style: italic;
    font-weight: 900;
    text-transform: uppercase;
    text-align: center;
    margin-bottom: 40px;
  }

  /* Police commune */
  #ajout_otr,
  #ajout_otr label,
  #ajout_otr input,
  #ajout_otr select,
  #ajout_otr textarea,
  #ajout_otr legend {
    font-family: 'Poppins', sans-serif;
  }

  /* Labels alignés */
  #ajout_otr label {
    display: inline-block;
    width: 200px;
    text-align: right;
    margin-right: 20px;
    font-size: 16px;
    vertical-align: middle;
  }

  /* Champs de saisie */
  #ajout_otr input[type="text"],
  #ajout_otr input[type="email"],
  #ajout_otr input[type="number"],
  #ajout_otr input[type="date"],
  #ajout_otr input[type="tel"],
  #ajout_otr select {
    width: calc(100% - 240px);
    max-width: 500px;
    padding: 8px 10px;
    font-size: 15px;
    margin-bottom: 15px;
    vertical-align: middle;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

  /* Checkbox */
  #ajout_otr input[type="checkbox"] {
    width: auto;
    margin-left: 0;
    transform: scale(1.2);
    vertical-align: middle;
  }

  /* Ligne label + champ sur une ligne */
  #ajout_otr label,
  #ajout_otr input,
  #ajout_otr select {
    display: inline-block;
  }

  /* Zone spécifique */
  #checkbox-equipes {
    margin-top: 20px;
  }

  #checkbox-equipes div {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    margin-left: 220px;
  }


  /* Bouton */
  #ajout_otr button[type="submit"] {
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

  #ajout_otr button[type="submit"]:hover {
    background-color: #69c10f;
    transform: scale(1.03);
  }

  /* Radios */
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

  .select2-container {
    margin-bottom: 15px !important;
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

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 1.5;
    padding-left: 0;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%;
    top: 0;
    right: 10px;
  }

  /* Couleur de fond du menu déroulant */
  .select2-container--default .select2-results > .select2-results__options {
    background-color: white; /* fond général */
    color: #333; /* couleur du texte */
  }

  /* Couleur de l'élément survolé */
  .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #7cda24; /* vert du club */
    color: white;
  }

  /* Couleur de l'élément sélectionné */
  .select2-container--default .select2-results__option[aria-selected="true"] {
    background-color: #7cda24; /* vert du club */
    color: white;
}
  

</style>

</head>

<body>
  <div class="ajout-container">
    <h1>Ajouter un OTR</h1>

    <legend>Type d'ajout :</legend>
    <div class="radio-options">
      <label><input type="radio" id="new" name="form_otr" value="new"> Nouvelle personne</label>
      <label><input type="radio" id="old" name="form_otr" value="old" checked> Personne existante</label>
    </div>

    <form id="ajout_otr">
      <?php wp_nonce_field('wp_rest'); ?>

      <!-- Sélection d'une personne -->
      <div id="select-personne">
        <label for="personne">Personne existante :</label>
        <select id="personne" name="personne" style="width: 300px" required>
          <option value="">Chargement...</option>
        </select>
      </div>

      <!-- Partie nouvelle personne -->
      <div id="new-personne-fields">
        <?php $hide_title = true; ?>
        <?php include MY_PLUGIN_PATH . '/includes/templates/part-personne-form.php'; ?>
      </div>

      <label for="niveauOTR">Niveau d'OTR :</label>
      <input type="text" name="niveauOTR" id="niveauOTR" required><br>

      <div id="checkbox-equipes"></div>

      <button type="submit">Ajouter</button>
    </form>
  </div>

  <script>
    jQuery(document).ready(function ($) {
      const $form = $("#ajout_otr");
      const $selectPersonne = $("#personne");
      const $selectPersonneBlock = $("#select-personne");
      const $newPersonneFields = $("#new-personne-fields");
      const $nom = $("#nom");
      const $prenom = $("#prenom");

      function chargerCheckboxEquipes() {
        $.ajax({
          url: "<?php echo get_rest_url(null, 'gestion-equipe/v1/equipessaison'); ?>",
          method: "GET",
          success: function (data) {
            const $container = $("#checkbox-equipes");
            $container.empty();

            data.forEach(function (equipe) {
              const id = equipe.equ_id;
              const nom = equipe.equ_cat;

              const checkboxHTML = `
                <div>
                  <label for="${id}">${nom}</label>
                  <input type="checkbox" name="equipes[]" value="${id}" id="${id}">
                </div>`;

              $container.append(checkboxHTML);
            });
          },
          error: function (error) {
            console.error("Erreur lors du chargement des équipes :", error);
            $("#checkbox-equipes").html("<p>Impossible de charger les équipes.</p>");
          }
        });
      }

      function chargerPersonnesDansSelect($select) {
        $select.empty().append('<option value="">Sélectionnez une personne</option>');

        $.ajax({
          url: "<?php echo get_rest_url(null, 'gestion-equipe/v1/personnesotrs'); ?>",
          method: "GET",
          success: function (data) {
            data.forEach(function (personne) {
              $select.append(`<option value="${personne.pers_id}">${personne.pers_nom} ${personne.pers_prenom}</option>`);
            });
            $select.trigger("change");
          },
          error: function (error) {
            alert("Erreur lors de la récupération des personnes");
            console.error(error);
          }
        });
      }

      // Init
      $selectPersonne.select2({ placeholder: "Sélectionnez une personne" });
      chargerPersonnesDansSelect($selectPersonne);
      chargerCheckboxEquipes();

      // Changement radio
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
      // Forcer l'affichage correct au chargement
      toggleFormType($('input[name="form_otr"]:checked').val() === "new");


      // Soumission
      $form.submit(function (event) {
        event.preventDefault();
        $.ajax({
          method: "POST",
          url: "<?php echo esc_url(rest_url('gestion-equipe/v1/addotr')); ?>",
          data: $form.serialize(),
          success: function (response) {
            alert("OTR ajouté avec succès !");
            window.location.href = "/liste-des-otrs";
          },
          error: function (error) {
            alert("Erreur lors de la création de l'OTR : " + error.responseJSON.message);
            console.error(error);
          },
        });
      });
    });
  </script>
</body>

</html>
