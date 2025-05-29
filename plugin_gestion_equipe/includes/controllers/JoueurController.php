<?php



class JoueurController
{
    private JoueurRepository $repo;

    public function __construct(JoueurRepository $repo)
    {
        $this->repo = $repo;
    }

    public function creerJoueur(array $data): int|false
    {
        return $this->repo->creerJoueur([
            'jou_pers_id' => (int) $data['personne'],
            'jou_num_maillot' => (int) $data['noMaillot'],
            'jou_actif' => (isset($data['isActif']) && $data['isActif'] === 'on') ? 1 : 0,
            'jou_pere' => !empty($data['parent1']) ? (int) $data['parent1'] : null,
            'jou_mere' => !empty($data['parent2']) ? (int) $data['parent2'] : null,
        ]);
    }



    public function getJoueurById(int $id): ?array
    {
        return $this->repo->getJoueurById($id);
    }

    public function modifierJoueur(int $id, array $data): bool
    {
        return $this->repo->modifierJoueur($id, [
            'jou_pers_id' => (int) $data['index'],
            'jou_num_maillot' => (int) $data['noMaillot'],
            'jou_actif' => (isset($data['isActif']) && $data['isActif'] === 'on') ? 1 : 0,
            'jou_pere' => !empty($data['parent1']) ? (int) $data['parent1'] : null,
            'jou_mere' => !empty($data['parent2']) ? (int) $data['parent2'] : null,
        ]);
    }

    public function modifierEquipeJoueur(int $id, int $idEquipe): bool
    {
        return $this->repo->modifierEquipeJoueur([
            'jouE_joueur_id' => (int) $id,
            'jouE_equipe_id' => (int) $idEquipe
        ]);
    }

    public function supprimerJoueur(int $id): bool
    {
        return $this->repo->supprimerJoueur($id);
    }

    public function getAllJoueurs(): array
    {
        return $this->repo->getAllJoueurs();
    }

    public function getAllJoueursByEquipe($data): array
    {
        return $this->repo->getAllJoueursByEquipe($data);
    }

    public function insererJoueurDansEquipe(int $joueur_id, int $equipe_id){
        return $this->repo->insererJoueurDansEquipe($joueur_id,$equipe_id);
    }
}
