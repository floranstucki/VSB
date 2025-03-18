<?php 
if (!isset($_GET['index'])) {
    echo "<p>Erreur : Aucun joueur sélectionné.</p>";
    return;
}

$index = (int) $_GET['index'];
$file_path = dirname(__dir__,2) . '/includes/joueur_form.json';

if (!file_exists($file_path)) {
    echo "<p>Aucun joueur enregistré.</p>";
    return;
}

$json_data = file_get_contents($file_path);
$joueurs = json_decode($json_data, true);

if (!isset($joueurs[$index])) {
    echo "<p>Joueur introuvable.</p>";
    return;
}

$joueur = $joueurs[$index];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un joueur</title>
</head>
<body>
    <h2>Modifier un joueur</h2>
    <form id="modifier-joueur-form">
        <?php wp_nonce_field('wp_rest'); ?>

        <!-- Champ caché pour l'index -->
        <input type="hidden" name="index" value="<?php echo $index; ?>">

        <label for="name">Nom du joueur</label>
        <input type="text" name="name" id="name" value="<?php echo $joueur['name']; ?>">

        <label for="firstName">Prénom du joueur</label>
        <input type="text" name="firstName" id="firstName" value="<?php echo $joueur['firstName']; ?>">

        <label for="dateNaissance">Date de naissance</label>
        <input type="date" name="dateNaissance" id="dateNaissance" value="<?php echo $joueur['dateNaissance']; ?>">

        <label for="address">Adresse</label>
        <input type="text" name="address" id="address" value="<?php echo $joueur['address']; ?>">

        <label for="npa">NPA/Lieu</label>
        <input type="text" name="npa" id="npa" value="<?php echo $joueur['npa']; ?>">

        <label for="equipe">Équipe</label>
        <select name="equipe" id="equipe">
            <option value="">Sélectionnez une équipe</option>
            <?php 
            $equipes = ["U08B", "U08A", "U10B", "U10A", "U12B", "U12A", "U14M3", "U14M2", "U14M1", "U16M2", "U16M1", "U18M2", "U18M1", "U18U20M", "2LCM", "1LNM"];
            foreach ($equipes as $equipe) {
                $selected = ($joueur['equipe'] == $equipe) ? 'selected' : '';
                echo "<option value='$equipe' $selected>$equipe</option>";
            }
            ?>
        </select>

        <button type="submit">Modifier</button>
    </form>

    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
jQuery(document).ready(function($){
    $('#modifier-joueur-form').submit(function(event){
        event.preventDefault();
        
        let formData = new FormData(this);

        $.ajax({
            type: "POST",
            url: "<?php echo esc_url(rest_url('v1/modifier-joueur-form/submit')); ?>",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert(response.message);
                window.location.href = "/liste-des-joueurs"; 
            },
            error: function(xhr) {
                console.error("Erreur AJAX:", xhr.responseText);
            }
        });
    });
});
</script>

</body>
</html>