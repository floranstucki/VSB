<?php
namespace VSB\services;

class ComptaStatsService {

    public function calculerStatsGlobale($comptas) {
        $totalEncaisse = 0;
        $totalAttendu = 0;
        $nbPayes = 0;
        $nbJoueurs = count($comptas);

        foreach ($comptas as $compta) {
            $totalAttendu += floatval($compta->compt_prix_tot);
            if ($compta->compt_paye) {
                $totalEncaisse += floatval($compta->compt_prix_tot);
                $nbPayes++;
            }
        }

        return [
            'total' => $totalEncaisse,
            'attendu' => $totalAttendu,
            'payes' => $nbPayes,
            'non_payes' => $nbJoueurs - $nbPayes,
            'moyenne' => $nbJoueurs ? round($totalEncaisse / $nbJoueurs, 2) : 0
        ];
    }
}
