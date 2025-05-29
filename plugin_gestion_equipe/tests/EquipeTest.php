<?php 

use PHPUnit\Framework\TestCase;
use GestionEquipe\Classes\Equipe;
class EquipeTest extends TestCase{
    public function testCreationEquipe()
    {
        $equipe = new Equipe(1, "Senior");

        $this->assertEquals(1, $equipe->getEqu_id());
        $this->assertEquals("Senior", $equipe->getEqu_cat());
    }

    public function getAllTeams(){
        $equipes = obtenir_equipes();

        $this->assertNotEmpty($equipes);
        $this-> assertIsArray($equipes);
        $this->assertEquals(16, count($equipes));
        foreach ($equipes as $equipe) {
            $this->assertArrayHasKey('equ_id', $equipe);
            $this->assertArrayHasKey('equ_cat', $equipe);
        }
        $this->assertContains(['equ_id' => 8, 'equ_cat' => 'U14M2'], $equipes);
        $this->expectNotToPerformAssertions();
    }
}