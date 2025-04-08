<?php

use PHPUnit\Framework\TestCase;
use GestionEquipe\Classes\Joueur;  
use GestionEquipe\Classes\Personne;
use GestionEquipe\Classes\Sexe;
use DateTime; 

class PersonneTest extends TestCase{
    public function testCreatePersonne(){
        // création du'une personne
        $personne = new Personne();
        $personne->setId(1);
        $personne->setName("Dupont");
        $personne->setFirstName("Jean");
        $personne->setDateNai(new DateTime("2000-01-01"));
        $personne->setSexe(Sexe::Homme);
        $personne->setNumLicence(123456);
        $personne->setLicenceIsOk(true);
        $personne->setLicenceA("Fédération Française de Football");
        $personne->setNationaliteUne("Française");
        $personne->setNationaliteDeux("Suisse");
        $personne->setAdresse("1 rue de la Paix");
        $personne->setNpa("75001");
        $personne->setTelephone("0123456789");

        $this->assertEquals(1, $personne->getId());
        $this->assertEquals("Dupont", $personne->getName());
        $this->assertEquals("Jean", $personne->getFirstName());
        $this->assertEquals(new DateTime("2000-01-01"), $personne->getDateNai());
        $this->assertEquals(Sexe::Homme, $personne->getSexe());
        $this->assertEquals(123456, $personne->getNumLicence());
        $this->assertTrue($personne->getLicenceIsOk());
        $this->assertEquals("Fédération Française de Football", $personne->getLicenceA());
        $this->assertEquals("Française", $personne->getNationaliteUne());
        $this->assertEquals("Suisse", $personne->getNationaliteDeux());
        $this->assertEquals("1 rue de la Paix", $personne->getAdresse());
        $this->assertEquals("75001", $personne->getNpa());
        $this->assertEquals("0123456789", $personne->getTelephone()); 
    }

    public function testObtenirPersonne(){
        // obtention des personnes
        $personnes = obtenir_personnes();
        $this->assertNotEmpty($personnes);
        $this->assertIsArray($personnes);
        $this->assertEquals(1, count($personnes));
    }
}