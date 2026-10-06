<?php

use PHPUnit\Framework\TestCase;
use Model\aguaQualidade;
use Controller\aguaController;

require_once __DIR__ . '/../vendor/autoload.php';

class aguaTest extends TestCase
{
    private aguaQualidade $model;

    protected function setUp(): void
    {
        $this->model = new aguaQualidade();
    }

    public function testPhDentroDoLimiteEPotavel(): void
    {
        $this->assertSame('Potável', $this->model->classifyParameter('ph', 7.2));
        $this->assertSame('Potável', $this->model->classifyParameter('ph', 6.0));
        $this->assertSame('Potável', $this->model->classifyParameter('ph', 9.5));
    }

    public function testPhForaDoLimite(): void
    {
        $this->assertNotSame('Potável', $this->model->classifyParameter('ph', 5.9));
        $this->assertNotSame('Potável', $this->model->classifyParameter('ph', 9.6));
    }

    public function testTurbidezMaxima(): void
    {
        $this->assertSame('Potável', $this->model->classifyParameter('turbidity', 5.0));
        $this->assertNotSame('Potável', $this->model->classifyParameter('turbidity', 5.1));
    }

    public function testCloroResidual(): void
    {
        $this->assertSame('Potável', $this->model->classifyParameter('chlorine', 0.2));
        $this->assertSame('Potável', $this->model->classifyParameter('chlorine', 2.0));
        $this->assertNotSame('Potável', $this->model->classifyParameter('chlorine', 0.1));
        $this->assertNotSame('Potável', $this->model->classifyParameter('chlorine', 2.1));
    }

    public function testParametroDesconhecidoLancaExcecao(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->model->classifyParameter('dureza', 1.0);
    }

    public function testBiofiltroReduzTurbidez(): void
    {
        $this->assertSame(4.55, $this->model->simulateBiofilter(6.5, 30));
        $this->assertSame(0.0, $this->model->simulateBiofilter(6.5, 100));
        $this->assertSame(6.5, $this->model->simulateBiofilter(6.5, 0));
    }

    public function testBiofiltroLimitaEficienciaEntre0e100(): void
    {
        $this->assertSame(0.0, $this->model->simulateBiofilter(6.5, 150));
        $this->assertSame(6.5, $this->model->simulateBiofilter(6.5, -10));
    }

    public function testControllerComValoresPadraoDoFormulario(): void
    {
        $controller = new aguaController($this->model);
        $r = $controller->processSample([
            'ph' => '7.2', 'turbidity' => '6.5', 'chlorine' => '0.8', 'biofilter_rate' => '30',
        ]);

        $this->assertFalse($r['before']['is_potable']);
        $this->assertSame(4.55, $r['after']['turbidity']['value']);
        $this->assertTrue($r['after']['is_potable']);
    }
}
