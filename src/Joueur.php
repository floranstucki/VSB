<?php
namespace GestionEquipe\Classes;

class Joueur
{
    private int $jou_id;

    private Personne $jou_pers_id;

    private int $jou_num_maillot;

    private bool $is_actif;

    private Personne $jou_pere;

    private Personne $jou_mere;


    public function __construct(int $jou_id, Personne $jou_pers_id, int $jou_num_maillot, bool $is_actif, Personne $jou_pere, Personne $jou_mere){
        $this->jou_id = $jou_id;
        $this->jou_pers_id = $jou_pers_id;
        $this->jou_num_maillot = $jou_num_maillot;
        $this->is_actif = $is_actif;
        $this->jou_pere = $jou_pere;
        $this->jou_mere = $jou_mere;
    }
    

    /**
     * Get the value of jou_id
     */ 
    public function getJou_id()
    {
        return $this->jou_id;
    }

    /**
     * Set the value of jou_id
     *
     * @return  self
     */ 
    public function setJou_id($jou_id)
    {
        $this->jou_id = $jou_id;

        return $this;
    }

    /**
     * Get the value of jou_pers_id
     */ 
    public function getJou_pers_id()
    {
        return $this->jou_pers_id;
    }

    /**
     * Set the value of jou_pers_id
     *
     * @return  self
     */ 
    public function setJou_pers_id($jou_pers_id)
    {
        $this->jou_pers_id = $jou_pers_id;

        return $this;
    }

    /**
     * Get the value of jou_num_maillot
     */ 
    public function getJou_num_maillot()
    {
        return $this->jou_num_maillot;
    }

    /**
     * Set the value of jou_num_maillot
     *
     * @return  self
     */ 
    public function setJou_num_maillot($jou_num_maillot)
    {
        $this->jou_num_maillot = $jou_num_maillot;

        return $this;
    }

    /**
     * Get the value of is_actif
     */ 
    public function getIs_actif()
    {
        return $this->is_actif;
    }

    /**
     * Set the value of is_actif
     *
     * @return  self
     */ 
    public function setIs_actif($is_actif)
    {
        $this->is_actif = $is_actif;

        return $this;
    }

    /**
     * Get the value of jou_pere
     */ 
    public function getJou_pere()
    {
        return $this->jou_pere;
    }

    /**
     * Set the value of jou_pere
     *
     * @return  self
     */ 
    public function setJou_pere($jou_pere)
    {
        $this->jou_pere = $jou_pere;

        return $this;
    }

    /**
     * Get the value of jou_mere
     */ 
    public function getJou_mere()
    {
        return $this->jou_mere;
    }

    /**
     * Set the value of jou_mere
     *
     * @return  self
     */ 
    public function setJou_mere($jou_mere)
    {
        $this->jou_mere = $jou_mere;

        return $this;
    }

    public function __tostring(){return "";}
}


?>