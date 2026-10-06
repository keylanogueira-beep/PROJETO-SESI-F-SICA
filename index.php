<?php
require_once __DIR__ . '/vendor/autoload.php';

use Model\aguaQualidade;
use Controller\aguaController;

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = new aguaQualidade();
    $controller = new aguaController($model);
    $result = $controller->processSample($_POST);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Laboratório Digital de Qualidade da Água</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <header class="mb-4 text-center">
            <h1 class="display-5 fw-bold text-primary">Lab Digital: Qualidade da Água e Biofiltro</h1>
            <p class="text-muted">Simulador de potabilidade e eficiência de filtragem (ODS 6)</p>
        </header>

        <div class="row g-4">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">Insira os Dados da Amostra</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">pH (6.0 a 9.5)</label>
                                <input type="number" step="0.1" name="ph" class="form-control" value="7.2" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Turbidez (NTU - Máx 5.0)</label>
                                <input type="number" step="0.1" name="turbidity" class="form-control" value="6.5" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Cloro Residual (mg/L - 0.2 a 2.0)</label>
                                <input type="number" step="0.1" name="chlorine" class="form-control" value="0.8" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Eficiência de Remoção do Biofiltro (%)</label>
                                <input type="number" step="1" name="biofilter_rate" class="form-control" value="30" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Analisar e Simular</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-secondary text-white">Resultados da Análise</div>
                    <div class="card-body">
                        <?php if ($result): ?>
                            <h5 class="fw-bold">1. Estado Inicial (Bruto)</h5>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>pH: <?= $result['before']['ph']['value'] ?></span>
                                    <span class="badge bg-<?= $result['before']['ph']['status'] === 'Potável' ? 'success' : 'danger' ?>"><?= $result['before']['ph']['status'] ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Turbidez: <?= $result['before']['turbidity']['value'] ?> NTU</span>
                                    <span class="badge bg-<?= $result['before']['turbidity']['status'] === 'Potável' ? 'success' : 'danger' ?>"><?= $result['before']['turbidity']['status'] ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Cloro: <?= $result['before']['chlorine']['value'] ?> mg/L</span>
                                    <span class="badge bg-<?= $result['before']['chlorine']['status'] === 'Potável' ? 'success' : 'danger' ?>"><?= $result['before']['chlorine']['status'] ?></span>
                                </li>
                            </ul>
                            <div class="alert alert-<?= $result['before']['is_potable'] ? 'success' : 'danger' ?>">
                                Parecer Inicial: <strong><?= $result['before']['is_potable'] ? 'Água Aprovada / Potável' : 'Água Fora dos Padrões' ?></strong>
                            </div>

                            <h5 class="fw-bold mt-4">2. Pós-Simulação do Biofiltro</h5>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Turbidez Tratada: <?= $result['after']['turbidity']['value'] ?> NTU</span>
                                    <span class="badge bg-<?= $result['after']['turbidity']['status'] === 'Potável' ? 'success' : 'danger' ?>"><?= $result['after']['turbidity']['status'] ?></span>
                                </li>
                            </ul>
                            <div class="alert alert-<?= $result['after']['is_potable'] ? 'success' : 'danger' ?>">
                                Parecer Final (Pós-Filtro): <strong><?= $result['after']['is_potable'] ? 'Água Potável' : 'Ainda Fora dos Padrões' ?></strong>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center my-5">Preencha o formulário ao lado e clique em "Analisar e Simular" para ver os resultados.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
