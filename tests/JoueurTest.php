<?php

use PHPUnit\Framework\TestCase;
use GestionEquipe\Classes\Joueur;  
use GestionEquipe\Classes\Personne;
use GestionEquipe\Classes\Sexe;
use DateTime; 

class JoueurTest extends TestCase{ //id name first_name date_nai,sexe,num_licence, licence_is_ok, licence_a nationalite_1 nationalite_2 adresse npa telephone email num avs entree club
    private $personne1;
    private $personne2;
    private $personne3;
    protected function setUp(): void {
        $this->personne1 = new Personne(1,'Doe','John',new DateTime('1990-05-15'),Sexe::Homme,123456,true, 'A','Française','Italienne','123 Rue Exemple','1000','0123456789','john.doe@example.com','AVS123456',new DateTime('2010-06-01'));
    
        $this->personne2 = new Personne(2,'Smith','Jane',new DateTime('1985-08-22'),Sexe::Femme,654321,true,'B','Anglaise','Espagnole','456 Boulevard Exemple','2000','0987654321','jane.smith@example.com','AVS654321',new DateTime('2015-09-10'));
    
        $this->personne3 = new Personne(3,'Durand','Pierre',new DateTime('2000-12-10'),Sexe::Homme,789012,false,'C','Française','Allemande','789 Avenue Test','3000','1234567890','pierre.durand@example.com','AVS789012',new DateTime('2018-11-20'));
    }

    public function testCreationJoueur(){
        $joueur = new Joueur(1,$this->personne1,77,true,$this->personne3,$this->personne2);
        $this->assertNotEmpty($joueur);
        $this->assertSame($this->personne1, $joueur->getJou_pers_id());
        $this->assertEquals(1,$joueur->getJou_pers_id()->getId());
        $this->assertEquals(77,$joueur->getJou_num_maillot());
        $this->assertTrue($joueur->getIs_actif());        
        $this->assertSame($this->personne3, $joueur->getJou_pere());
        $this->assertSame($this->personne2, $joueur->getJou_mere());
    }

    public function testUpdateJoueur(){
        $joueur = new Joueur(1,$this->personne1,77,true,$this->personne3,$this->personne2);
        $joueur->setJou_num_maillot(10);
        $this->assertEquals(10,$joueur->getJou_num_maillot());
        $joueur->setIs_actif(false);
        $this->assertFalse($joueur->getIs_actif());
    }

    public function testDeleteJoueur(){
        $joueur = new Joueur(1,$this->personne1,77,true,$this->personne3,$this->personne2);
        $this->assertNotEmpty($joueur);
        unset($joueur);
        $this->assertFalse(isset($joueur)); // Vérifie que le joueur a été supprimé
    }

    public function testGetAllJoueurs(){
        $joueur1 = new Joueur(1,$this->personne1,77,true,$this->personne3,$this->personne2);
        $joueur2 = new Joueur(2,$this->personne2,10,false,$this->personne3,$this->personne1);
        $joueur3 = new Joueur(3,$this->personne3,15,true,$this->personne1,$this->personne2);
        
        $joueurs = [$joueur1, $joueur2, $joueur3];
        
        $this->assertCount(3, $joueurs);
        $this->assertSame($joueur1, $joueurs[0]);
        $this->assertSame($joueur2, $joueurs[1]);
        $this->assertSame($joueur3, $joueurs[2]);
    }


}