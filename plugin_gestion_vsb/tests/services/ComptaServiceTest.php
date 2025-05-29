<?php
use PHPUnit\Framework\TestCase;
use VSB\Services\ComptaService;

class ComptaServiceTest extends TestCase {

    private $service;

    protected function setUp(): void {
        $this->service = new ComptaService();
    }

    public function testCalculerTotalSansRabais() {
        $total = $this->service->calculerTotal(200, 0, 100, 0, 50);
        $this->assertEquals(350, $total);
    }

    public function testCalculerTotalAvecRabais() {
        $total = $this->service->calculerTotal(200, 50, 100, 20, 30);
        $this->assertEquals(260, $total); // 150 + 80 + 30
    }

    public function testBadgePaiement() {
        $this->assertStringContainsString('✔️', $this->service->getBadgePaiement(1));
        $this->assertStringContainsString('❌', $this->service->getBadgePaiement(0));
    }

    public function testPreparerDonneesCompta() {
        $data = $this->service->preparerDonneesCompta(42, 1, [
            'cotisation' => 300,
            'rabais_cotisation' => 50,
            'licence' => 100,
            'rabais_licence' => 25,
            'frais_entree' => 20,
            'paye' => 1
        ]);

        $this->assertEquals(42, $data['compt_pers_id']);
        $this->assertEquals(1, $data['compt_sai_id']);
        $this->assertEquals(345, $data['compt_prix_tot']);
        $this->assertEquals(1, $data['compt_paye']);
    }
}
