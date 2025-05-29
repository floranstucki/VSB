<?php
namespace VSB\controllers;

use VSB\repositories\RessourceRepository;
use VSB\services\FileService;

class RessourceController {
    public function registerShortcodes() {
        add_shortcode('vsb_ressources', [$this, 'afficherFormulaireUpload']);
        add_shortcode('vsb_liste_ressources', [$this, 'afficherListeRessources']);
    }

    public function afficherFormulaireUpload() {
        $repo = new RessourceRepository();
        $fileService = new FileService();
        $message = '';

        $user = wp_get_current_user();
        $coach_id = 1; //$repo->getCoachIdByUserEmail($user->user_email);

        if (!empty($_FILES['ress_fichier']['name'])) {
            $titre = sanitize_text_field($_POST['ress_titre']);
            $desc = sanitize_textarea_field($_POST['ress_desc']);
            $type = sanitize_text_field($_POST['ress_type']);
            $url = $fileService->upload($_FILES['ress_fichier']);

            if ($url && $coach_id) {
                $repo->ajouter($titre, $desc, $type, $url, $coach_id);
                $message = "<div class='notice notice-success'>Ressource ajoutée.</div>";
            }
        }

        ob_start();
        echo $message;
        ?>
        <!-- Polices -->
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            .ajout-container {
                max-width: 960px;
                margin: 0 auto;
                padding: 20px;
                display: block;
                text-align: center;
            }

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

            .ajout-container label {
                display: inline-block;
                width: 200px;
                text-align: right;
                margin-right: 20px;
                font-size: 16px;
                vertical-align: middle;
                font-family: 'Poppins', sans-serif;
            }

            .ajout-container input[type="text"],
            .ajout-container input[type="file"],
            .ajout-container select,
            .ajout-container textarea {
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

            .ajout-container input[type="submit"] {
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

            .ajout-container input[type="submit"]:hover {
                background-color: #69c10f;
                transform: scale(1.03);
            }
        </style>
        
        <div class="ajout-container">
            <h1>Ajouter une ressource</h1>
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="ress_titre" placeholder="Titre" required><br>
                <textarea name="ress_desc" placeholder="Description" required></textarea><br>
                <select name="ress_type">
                    <option value="exercice">Exercice</option>
                    <option value="tactique">Tactique</option>
                </select><br>
                <input type="file" name="ress_fichier" accept=".doc,.docx,.pdf" required><br>
                <input type="submit" value="Uploader">
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function afficherListeRessources() {
        $repo = new RessourceRepository();
        $message = '';

        $type = $_GET['filtre_type'] ?? '';
        $coach = $_GET['filtre_coach'] ?? '';
        $recherche = $_GET['filtre_titre'] ?? '';

        // Traitement édition
        if (isset($_POST['ress_update_id'])) {
            $id = intval($_POST['ress_update_id']);
            $titre = sanitize_text_field($_POST['ress_titre']);
            $desc = sanitize_textarea_field($_POST['ress_desc']);
            $typeUpdate = sanitize_text_field($_POST['ress_type']);
            $repo->modifier($id, $titre, $desc, $typeUpdate);
            $message = "<div class='notice notice-success'>Ressource modifiée.</div>";
        }

        // Suppression
        if (isset($_GET['delete'])) {
            $repo->supprimer(intval($_GET['delete']));
            $message = "<div class='notice notice-success'>Ressource supprimée.</div>";
        }

        $ressources = $repo->getFiltered($type, $coach, $recherche);
        $coachs = $repo->getListeCoachs();

        // Récupération pour édition
        $ressource_a_modifier = null;
        if (isset($_GET['edit'])) {
            $ressource_a_modifier = $repo->getById(intval($_GET['edit']));
        }

        ob_start();
        echo $message;
        ?>

        <style>
            .filtres-container {
                max-width: 960px;
                margin: 0 auto 40px;
                padding: 20px;
                font-family: 'Poppins', sans-serif;
                background: #f9f9f9;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .filtres-container h1 {
                font-family: 'Montserrat', sans-serif;
                font-size: 36px;
                font-style: italic;
                text-transform: uppercase;
                color: #7cda24;
                text-align: center;
                margin-bottom: 20px;
            }

            .filtres-container form {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 20px;
            }

            .filtres-container input[type="text"],
            .filtres-container select {
                font-family: 'Poppins', sans-serif;
                padding: 10px 14px;
                font-size: 15px;
                border: 1px solid #ccc;
                border-radius: 5px;
                width: 220px;
                box-sizing: border-box;
            }

            .filtres-container input[type="submit"] {
                background-color: #7cda24;
                color: white;
                font-weight: 600;
                font-size: 15px;
                padding: 10px 22px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
            }

            .filtres-container input[type="submit"]:hover {
                background-color: #69c10f;
                transform: scale(1.03);
            }
        </style>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <div class="filtres-container">
            <h1>Filtres</h1>
            <form method="get">
                <input type="hidden" name="page_id" value="<?= get_the_ID() ?>">
                <input type="text" name="filtre_titre" placeholder="Titre..." value="<?= esc_attr($recherche) ?>">
                <select name="filtre_type">
                    <option value="">Tous types</option>
                    <option value="exercice" <?= $type == 'exercice' ? 'selected' : '' ?>>Exercice</option>
                    <option value="tactique" <?= $type == 'tactique' ? 'selected' : '' ?>>Tactique</option>
                </select>
                <select name="filtre_coach">
                    <option value="">Tous les coachs</option>
                    <?php foreach ($coachs as $c): ?>
                        <option value="<?= $c->coa_id ?>" <?= $coach == $c->coa_id ? 'selected' : '' ?>>
                            <?= esc_html($c->pers_prenom . ' ' . $c->pers_nom) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="Filtrer">
            </form>
            <?php if ($ressource_a_modifier): ?>
                <h1>Modifier la ressource</h1>
                <form method="post">
                    <input type="hidden" name="ress_update_id" value="<?= $ressource_a_modifier->ress_id ?>">
                    <input type="text" name="ress_titre" value="<?= esc_attr($ressource_a_modifier->ress_titre) ?>" required><br>
                    <textarea name="ress_desc" required><?= esc_textarea($ressource_a_modifier->ress_desc) ?></textarea><br>
                    <select name="ress_type">
                        <option value="exercice" <?= $ressource_a_modifier->ress_type == 'exercice' ? 'selected' : '' ?>>Exercice</option>
                        <option value="tactique" <?= $ressource_a_modifier->ress_type == 'tactique' ? 'selected' : '' ?>>Tactique</option>
                    </select><br>
                    <input type="submit" value="Modifier">
                </form>
            <?php endif; ?>
            <br>
            
            <h1>Toutes les ressources</h1>
            <ul>
            <?php foreach ($ressources as $r): ?>
                <li>
                    <strong><?= esc_html($r->ress_titre) ?></strong> (<?= $r->ress_type ?>)
                    – <?= esc_html($r->ress_desc) ?><br>
                    <a href="<?= esc_url($r->ress_url_fichier) ?>" download>Télécharger</a>
                    – <a href="?page_id=<?= get_the_ID() ?>&delete=<?= $r->ress_id ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                    – <a href="?page_id=<?= get_the_ID() ?>&edit=<?= $r->ress_id ?>">Modifier</a>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }
}
