<?php
use PHPUnit\Framework\TestCase;
use VSB\Services\ComptaStatsService;

class ComptaStatsServiceTest extends TestCase {

    private ComptaStatsService $service;

    protected function setUp(): void {
        $this->service = new ComptaStatsService();
    }

    public function testCalculerStatsGlobaleAvecTousPayes() {
        $comptas = [
            (object)['compt_prix_tot' => 100, 'compt_paye' => 1],
            (object)['compt_prix_tot' => 150, 'compt_paye' => 1]
        ];

        $stats = $this->service->calculerStatsGlobale($comptas);

        $this->assertEquals(250, $stats['total']);
        $this->assertEquals(250, $stats['attendu']);
        $this->assertEquals(2, $stats['payes']);
        $this->assertEquals(0, $stats['non_payes']);
        $this->assertEquals(125, $stats['moyenne']);
    }

    public function testCalculerStatsGlobaleMixte() {
        $comptas = [
            (object)['compt_prix_tot' => 100, 'compt_paye' => 1],
            (object)['compt_prix_tot' => 200, 'compt_paye' => 0],
        ];

        $stats = $this->service->calculerStatsGlobale($comptas);

        $this->assertEquals(100, $stats['total']);
        $this->assertEquals(300, $stats['attendu']);
        $this->assertEquals(1, $stats['payes']);
        $this->assertEquals(1, $stats['non_payes']);
        $this->assertEquals(50, $stats['moyenne']);
    }

    public function testCalculerStatsGlobaleVide() {
        $stats = $this->service->calculerStatsGlobale([]);
        $this->assertEquals(0, $stats['total']);
        $this->assertEquals(0, $stats['attendu']);
        $this->assertEquals(0, $stats['payes']);
        $this->assertEquals(0, $stats['non_payes']);
        $this->assertEquals(0, $stats['moyenne']);
    }
}
