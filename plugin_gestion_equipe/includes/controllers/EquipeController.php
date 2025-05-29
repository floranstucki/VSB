<?php

class EquipeController {
    private EquipeRepository $repo;

    public function __construct(EquipeRepository $repo) {
        $this->repo = $repo;
    }

    public function getEquipesSaisonEnCours(): array {
        return $this->repo->getEquipesSaisonEnCours();
    }

    public function getEquipes(): array {
        return $this->repo->getAllEquipes();
    }
    
}