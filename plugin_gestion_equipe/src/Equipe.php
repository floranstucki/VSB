<?php

namespace GestionEquipe\Classes;

class Equipe{
    private int $equ_id;

    private string $equ_cat;

    
    public function __construct(int $equ_id, string $equ_cat){
        $this->equ_id = $equ_id;
        $this->equ_cat = $equ_cat;
    }

    /**
     * Get the value of eq_id
     */ 
    public function getEqu_id()
    {
        return $this->equ_id;
    }

    /**
     * Set the value of eq_id
     *
     * @return  self
     */ 
    public function setEqu_id($equ_id)
    {
        $this->equ_id = $equ_id;

        return $this;
    }

    /**
     * Get the value of equ_cat
     */ 
    public function getEqu_cat()
    {
        return $this->equ_cat;
    }

    /**
     * Set the value of equ_cat
     *
     * @return  self
     */ 
    public function setEqu_cat($equ_cat)
    {
        $this->equ_cat = $equ_cat;

        return $this;
    }
}