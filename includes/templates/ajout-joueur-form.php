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
        </select>

        <button type="submit">Ajouter</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        jQuery(document).ready(function($){
            // Récupérer les équipes via l'API REST
            $.ajax({
                url: "<?php echo get_rest_url(null,'v1/equipes'); ?>",
                method: "GET",
                success:function(data){
                    let select = $('#equipe');
                    select.empty(); // vide les anciennes options
                    select.append('<option value="">Sélectionnez une équipe</option>');
                    data.forEach(function(equipe){
                        select.append('<option value="'+equipe.equ_id+'">'+equipe.equ_cat+'</option>');
                    });
                },
                error:function(error){
                    alert("Erreur lors de la récupération des équipes", error);
                }
            });

            // Soumettre le formulaire
            $('#ajout_joueur').submit(function(event){
                event.preventDefault();
                var form = $(this);
                console.log(form.serialize());
                $.ajax({
                    type: "POST",
                    url: "<?php echo get_rest_url(null,'v1/ajout-joueur-form/submit'); ?>",
                    data: form.serialize(),
                    success: function(response){
                        alert("Joueur ajouté avec succès !");
                        form.trigger("reset"); // Réinitialise le formulaire
                        window.location.href = "/liste-joueurs"; // Redirige vers la liste des joueurs
                    },
                    error: function(error){
                        alert("Erreur lors de l'ajout du joueur : " + error.responseJSON.message);
                    }
                })
            });
        });
    
    </script>
</body>
</html>
    


    