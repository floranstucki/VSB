<?php
namespace GestionEquipe\Classes;

use DateTime;
  enum Sexe {
    case Homme;
    case Femme;
}
class Personne
{

    private int $id;
    private string $name;

    private string $first_name;

    private DateTime $date_nai;

    private Sexe $sexe;

    private int $num_licence;
    private bool $licence_is_ok;
    private string $licence_a;
    private string $nationalite_une; 
    private string $nationalite_deux;
    private string $adresse;
    private string $npa;
    private string $telephone;
    private string $email;
    private string $num_avs;
    private DateTime $entree_club;

    public function __construct(int $id, string $name, string $first_name, DateTime $date_nai, Sexe $sexe, int $num_licence, bool $licence_is_ok, string $licence_a, string $nationalite_une,string $nationalite_deux, string $adresse, string $npa, string $telephone, string $email, string $num_avs, DateTime $entree_club){
      $this->id = $id;
      $this->name = $name;
      $this->first_name = $first_name;
      $this->date_nai = $date_nai;
      $this->sexe = $sexe;
      $this->num_licence = $num_licence;
      $this->licence_is_ok = $licence_is_ok;
      $this->licence_a = $licence_a;
      $this->nationalite_une = $nationalite_une;
      $this->nationalite_deux = $nationalite_deux;
      $this->adresse = $adresse;
      $this->npa = $npa;
      $this->telephone = $telephone;
      $this->email = $email;
      $this->num_avs = $num_avs;
      $this->entree_club = $entree_club;
    }
    
    
  




    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of first_name
     */ 
    public function getFirst_name()
    {
        return $this->first_name;
    }

    /**
     * Set the value of first_name
     *
     * @return  self
     */ 
    public function setFirst_name($first_name)
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * Get the value of date_nai
     */ 
    public function getDate_nai()
    {
        return $this->date_nai;
    }

    /**
     * Set the value of date_nai
     *
     * @return  self
     */ 
    public function setDate_nai($date_nai)
    {
        $this->date_nai = $date_nai;

        return $this;
    }

    /**
     * Get the value of sexe
     */ 
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set the value of sexe
     *
     * @return  self
     */ 
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get the value of num_licence
     */ 
    public function getNum_licence()
    {
        return $this->num_licence;
    }

    /**
     * Set the value of num_licence
     *
     * @return  self
     */ 
    public function setNum_licence($num_licence)
    {
        $this->num_licence = $num_licence;

        return $this;
    }

    /**
     * Get the value of licence_is_ok
     */ 
    public function getLicence_is_ok()
    {
        return $this->licence_is_ok;
    }

    /**
     * Set the value of licence_is_ok
     *
     * @return  self
     */ 
    public function setLicence_is_ok($licence_is_ok)
    {
        $this->licence_is_ok = $licence_is_ok;

        return $this;
    }

    /**
     * Get the value of licence_a
     */ 
    public function getLicence_a()
    {
        return $this->licence_a;
    }

    /**
     * Set the value of licence_a
     *
     * @return  self
     */ 
    public function setLicence_a($licence_a)
    {
        $this->licence_a = $licence_a;

        return $this;
    }

    /**
     * Get the value of nationalite_une
     */ 
    public function getNationalite_une()
    {
        return $this->nationalite_une;
    }

    /**
     * Set the value of nationalite_une
     *
     * @return  self
     */ 
    public function setNationalite_une($nationalite_une)
    {
        $this->nationalite_une = $nationalite_une;

        return $this;
    }

    /**
     * Get the value of nationalite_deux
     */ 
    public function getNationalite_deux()
    {
        return $this->nationalite_deux;
    }

    /**
     * Set the value of nationalite_deux
     *
     * @return  self
     */ 
    public function setNationalite_deux($nationalite_deux)
    {
        $this->nationalite_deux = $nationalite_deux;

        return $this;
    }

    /**
     * Get the value of adresse
     */ 
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set the value of adresse
     *
     * @return  self
     */ 
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get the value of npa
     */ 
    public function getNpa()
    {
        return $this->npa;
    }

    /**
     * Set the value of npa
     *
     * @return  self
     */ 
    public function setNpa($npa)
    {
        $this->npa = $npa;

        return $this;
    }

    /**
     * Get the value of telephone
     */ 
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set the value of telephone
     *
     * @return  self
     */ 
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of num_avs
     */ 
    public function getNum_avs()
    {
        return $this->num_avs;
    }

    /**
     * Set the value of num_avs
     *
     * @return  self
     */ 
    public function setNum_avs($num_avs)
    {
        $this->num_avs = $num_avs;

        return $this;
    }

    /**
     * Get the value of entree_club
     */ 
    public function getEntree_club()
    {
        return $this->entree_club;
    }

    /**
     * Set the value of entree_club
     *
     * @return  self
     */ 
    public function setEntree_club($entree_club)
    {
        $this->entree_club = $entree_club;

        return $this;
    }


    public function __tostring(){
      return "";
    }
}



?>