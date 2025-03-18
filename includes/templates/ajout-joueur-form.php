<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Équipe</title>
</head>

<body>
    <h2>Ajouter un joueur</h2>
    <form id="ajout_joueur">
        <?php wp_nonce_field('wp_rest'); ?>
        <label for="name">Nom du joueur</label>
        <input type="text" name="name" id="name" required>

        <label for="firstName">Prénom du joueur</label>
        <input type="text" name="firstName" id="firstName" required>

        <label for="dateNaissance">Date de naissance</label>
        <input type="date" name="dateNaissance" id="dateNaissance" required>

        <label for="address">Adresse</label>
        <input type="text" name="address" id="address" required>

        <label for="npa">NPA/Lieu</label>
        <input type="text" name="npa" id="npa" required>

        <label for="equipe">Équipe</label>
        <select name="equipe" id="equipe" required>
            <option value="">Sélectionnez une équipe</option>
            <?php
            $equipes = [
                "U08B",
                "U08A",
                "U10B",
                "U10A",
                "U12B",
                "U12A",
                "U14M3",
                "U14M2",
                "U14M1",
                "U16M2",
                "U16M1",
                "U18M2",
                "U18M1",
                "U18U20M",
                "2LCM",
                "1LNM"
            ];

            $selectedEquipe = isset($_GET['equipe']) ? $_GET['equipe'] : '';

            foreach ($equipes as $equipe) {
                $selected = ($equipe == $selectedEquipe) ? 'selected' : '';
                echo "<option value='$equipe' $selected>$equipe</option>";
            }
            ?>
        </select>


        <button type="submit">Ajouter</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {
            $('#ajout_joueur').submit(function (event) {
                event.preventDefault();
                var form = $(this);
                console.log(form.serialize());
                $.ajax({
                    type: "POST",
                    url: "<?php echo get_rest_url(null, 'v1/ajout-joueur-form/submit'); ?>",
                    data: form.serialize()
                })
            });
        });

    </script>
</body>

</html>