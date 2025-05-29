<?php


class OtrController
{
    private OtrRepository $repo;

    public function __construct(OtrRepository $repo)
    {
        $this->repo = $repo;
    }

    public function creerOTR(array $data): int|false
    {
        return $this->repo->creerOTR([
            'otr_pers_id' => (int) $data['personne'],
            'otr_niveau_otr' => sanitize_text_field($data['niveauOTR']),
        ]);
    }

    public function getOTRById(int $id): ?array
    {
        return $this->repo->getOTRById($id);
    }

    public function modifierOTR(int $id, array $data): bool
    {
        return $this->repo->modifierOTR($id, [
            'otr_pers_id' => (int) $data['index'],
            'otr_niveau_otr' => sanitize_text_field($data['niveauOTR']),
        ]);
    }

    public function supprimerOTR(int $id): bool
    {
        return $this->repo->supprimerOTR($id);
    }

    public function getAllOTR(): array
    {
        return $this->repo->getAllOTR();
    }

    public function insererOTREquipe($idOTR, $idEquipe){
        return $this->repo->insererOTREquipe($idOTR,$idEquipe);
    }
}
