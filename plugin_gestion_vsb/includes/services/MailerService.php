<?php
namespace VSB\services;

class MailerService {

    public function envoyerMailDispoOTR($to, $prenom, $equipe, $date, $heure, $lieu, $adversaire) {
        $sujet = "Disponibilité pour officier un match";
        $message = "Bonjour $prenom,<br><br>";
        $message .= "Peux-tu officier le match de l’équipe <strong>$equipe</strong> ";
        $message .= "le <strong>$date</strong> à <strong>$heure</strong> au <strong>$lieu</strong> ";
        $message .= "contre <strong>$adversaire</strong> ?<br><br>Merci de ta réponse rapide.";
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($to, $sujet, $message, $headers);
    }

    public function envoyerMailConfirmationRole($to, $prenom, $equipe, $role, $date, $heure, $lieu) {
        $sujet = "Confirmation rôle OTR – Match $equipe";
        $message = "Bonjour $prenom,<br><br>";
        $message .= "Tu as été assigné comme <strong>$role</strong> pour le match de l’équipe <strong>$equipe</strong> ";
        $message .= "le <strong>$date</strong> à <strong>$heure</strong> au <strong>$lieu</strong>.<br><br>";
        $message .= "Merci pour ton aide !";
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($to, $sujet, $message, $headers);
    }
}
