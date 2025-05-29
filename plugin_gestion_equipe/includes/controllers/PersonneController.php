<?php


class PersonneController
{

    private PersonneRepository $repo;

    public function __construct(PersonneRepository $repo)
    {
        $this->repo = $repo;
    }

    public function creerPersonne(array $data): int|false
    {
        return $this->repo->creerPersonne([
            'pers_nom' => sanitize_text_field($data['name']),
            'pers_prenom' => sanitize_text_field($data['firstName']),
            'pers_date_nai' => sanitize_text_field($data['dateNaissance']),
            'pers_sexe' => $data['sexe'],
            'pers_num_licence' => (int) $data['noLicence'],
            'pers_licence_ok' => !empty($data['licenceOk']) && $data['licenceOk'] === 'on',
            'pers_licence_a' => 'VSBC',
            'pers_nationalite_une' => sanitize_text_field($data['nationaliteUne']),
            'pers_nationalite_deux' => sanitize_text_field($data['nationaliteDeux']),
            'pers_adresse' => sanitize_textarea_field($data['address']),
            'pers_NPA' => sanitize_text_field($data['npa']),
            'pers_telephone' => sanitize_text_field($data['noTelephone']),
            'pers_mail' => sanitize_email($data['email']),
            'pers_num_avs' => null,
            'pers_entree_club' => sanitize_text_field($data['dateClub']),
        ]);
    }


    public function getAllPersonnes(): array
    {
        return $this->repo->getAllPersonnes();
    }

    public function getPersonneById($id): array
    {
        return $this->repo->getPersonneById($id);
    }

    public function getPersonnesNotJoueurs(): array
    {
        return $this->repo->getPersonnesNotJoueurs();
    }

    public function getPersonnesNotOtrs(): array
    {
        return $this->repo->getPersonnesNotOtrs();
    }

     public function modifierPersonne(int $id, array $data): bool
{
    return $this->repo->modifierPersonne($id, [
        'pers_nom'              => $data['name'] ?? '',
        'pers_prenom'           => $data['firstName'] ?? '',
        'pers_date_nai'         => $data['dateNaissance'] ?? null,
        'pers_sexe'             => $data['sexe'] ?? '',
        'pers_num_licence'      => $data['noLicence'] ?? '',
        'pers_licence_ok'       => isset($data['licenceOk']) && $data['licenceOk'] === 'on' ? 1 : 0,
        'pers_nationalite_une'  => $data['nationaliteUne'] ?? '',
        'pers_nationalite_deux' => $data['nationaliteDeux'] ?? '',
        'pers_adresse'          => $data['address'] ?? '',
        'pers_npa'              => $data['npa'] ?? '',
        'pers_telephone'        => $data['noTelephone'] ?? '',
        'pers_mail'            => $data['email'] ?? '',
        'pers_num_avs'          => null,
        'pers_entree_club'      => $data['dateClub'] ?? null,
    ]);
}


    public function supprimerPersonne(int $id): bool
    {
        return $this->repo->supprimerPersonne($id);
    }

}