<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajout d'une personne</title>
</head>

<body>
  <?php if (!isset($hide_title)) : ?>
    <h1 class="titre-ajout">Ajouter une personne</h1>
  <?php endif; ?>
  <?php wp_nonce_field('wp_rest'); ?>
  <label for="name">Nom de famille</label>
  <input type="text" name="name" id="name"><br>

  <label for="firstName">Prénom</label>
  <input type="text" name="firstName" id="firstName"><br>

  <label for="dateNaissance">Date de naissance</label>
  <input type="date" name="dateNaissance" id="dateNaissance"><br>

  <label for="sexe">Sexe</label>
  <select name="sexe" id="sexe">
    <option value="">Sélectionner</option>
    <option value="homme">Homme</option>
    <option value="femme">Femme</option>
  </select><br>

  <label for="noLicence">Numéro de licence</label>
  <input type="text" name="noLicence" id="noLicence" inputmode="numeric" pattern="[0-9]*"><br>

  <label for="equipe">Licence ok ?</label>
  <input type="checkbox" name="licenceOk" id="licenceOk"><br>

  <label for="nationaliteUne">Nationalité Une :</label>
  <input type="text" name="nationaliteUne" id="nationaliteUne"><br>

  <label for="nationaliteDeux">Nationalité Deux :</label>
  <input type="text" name="nationaliteDeux" id="nationaliteDeux"><br>

  <label for="address">Adresse</label>
  <input type="text" name="address" id="address"><br>

  <label for="npa">NPA/Lieu</label>
  <input type="text" name="npa" id="npa"><br>

  <label for="noTelephone">Numéro de téléphone</label>
  <input type="text" name="noTelephone" id="noTelephone"><br>

  <label for="email">Email</label>
  <input type="email" name="email" id="email"><br>


  <label for="dateClub">Date d'entrée au club :</label>
  <input type="date" name="dateClub" id="dateClub"><br>

</body>

</html>