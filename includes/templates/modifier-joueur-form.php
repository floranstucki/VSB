<?php 
require_once dirname(__DIR__) . '/fonctions.php';
// Récupérer l'index de joueur passé en paramètre (GET)
$joueur_id = isset($_GET['jou_id']) ? (int) $_GET['jou_id'] : 0;
//$file_path = plugin_dir_path(__FILE__) . 'joueur_form.json';

// Vérifier si le joueur existe
if ($joueur_id == 0) {
    echo "<p>Erreur : Aucun joueur sélectionné.</p>";
    return;
}

global $wpdb;
$joueur = $wpdb->get_row($wpdb->prepare("
    SELECT j.*, p.*, je.jouE_equipe_id 
    FROM vsb_joueur j
    JOIN vsb_personne p ON j.jou_pers_id = p.pers_id
    LEFT JOIN vsb_joueurEquipe je ON je.jouE_joueur_id = j.jou_id
    WHERE j.jou_id = %d
", $joueur_id), ARRAY_A);

// Vérifier si le joueur existe dans la base
if (!$joueur) {
    echo "<p>Joueur introuvable.</p>";
    return;
}

$equipes = obtenir_equipes(); // Récupérer la liste des équipes

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
        <input type="hidden" name="jou_id" value="<?php echo $joueur['jou_id']; ?>">

        <label for="name">Nom du joueur</label>
        <input type="text" name="pers_nom" id="pers_nom" value="<?php echo $joueur['pers_nom']; ?>">

        <label for="firstName">Prénom du joueur</label>
        <input type="text" name="pers_prenom" id="pers_prenom" value="<?php echo $joueur['pers_prenom']; ?>">

        <label for="dateNaissance">Date de naissance</label>
        <input type="date" name="pers_date_nai" id="pers_date_nai" value="<?php echo $joueur['pers_date_nai']; ?>">

        <label for="address">Adresse</label>
        <input type="text" name="pers_adresse" id="pers_adresse" value="<?php echo $joueur['pers_adresse']; ?>">

        <label for="npa">NPA/Lieu</label>
        <input type="text" name="pers_NPA" id="pers_NPA" value="<?php echo $joueur['pers_NPA']; ?>">

        <label for="equipe">Équipe</label>
        <select name="equipe" id="equipe">
            <option value="">Sélectionnez une équipe</option>
            <?php 
            foreach ($equipes as $equipe) {
                $selected = ($joueur['jouE_equipe_id'] == $equipe['equ_id']) ? 'selected' : '';
                echo "<option value='{$equipe['equ_id']}' $selected>{$equipe['equ_cat']}</option>";
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
                window.location.href = "/liste-joueurs"; 
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