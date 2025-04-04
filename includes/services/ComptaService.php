<?php
namespace VSB\services;

class ComptaService {

    
    public function calculerTotal($prixCotisation, $rabaisCoti, $prixLicence, $rabaisLicence, $fraisEntree) {
        $total = 0;

        if (is_numeric($prixCotisation)) {
            $total += max(0, $prixCotisation - $rabaisCoti);
        }

        if (is_numeric($prixLicence)) {
            $total += max(0, $prixLicence - $rabaisLicence);
        }

        if (is_numeric($fraisEntree)) {
            $total += $fraisEntree;
        }

        return $total;
    }

    public function preparerDonneesCompta($pers_id, $saison_id, $post) {
        $prixCotisation = floatval($post['cotisation'] ?? 0);
        $rabaisCotisation = floatval($post['rabais_cotisation'] ?? 0);
        $prixLicence = floatval($post['licence'] ?? 0);
        $rabaisLicence = floatval($post['rabais_licence'] ?? 0);
        $fraisEntree = floatval($post['frais_entree'] ?? 0);
        $paye = isset($post['paye']) ? intval($post['paye']) : 0;

        $total = $this->calculerTotal($prixCotisation, $rabaisCotisation, $prixLicence, $rabaisLicence, $fraisEntree);

        return [
            'compt_pers_id'        => $pers_id,
            'compt_sai_id'         => $saison_id,
            'compt_prix_cotisation'=> $prixCotisation,
            'compt_rabais_coti'    => $rabaisCotisation,
            'compt_prix_licence'   => $prixLicence,
            'compt_rabais_licence' => $rabaisLicence,
            'compt_frais_dentree'  => $fraisEntree,
            'compt_prix_tot'       => $total,
            'compt_paye'           => $paye
        ];
    }


    public function getBadgePaiement($paye) {
        if ($paye) {
            return "<span style='color: green;'>✔️ Payé</span>";
        } else {
            return "<span style='color: red;'>❌ Non payé</span>";
        }
    }
}
