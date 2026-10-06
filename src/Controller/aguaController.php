<?php

namespace Controller;

use Model\aguaController;

class WaterController
{
    private aguaController $aguaModel;

    public function __construct(aguaController $aguaModel)
    {
        $this->aguaModel = $aguaModel;
    }

    public function processSample(array $data): array
    {
        $ph = (float)($data['ph'] ?? 0);
        $turbidity = (float)($data['turbidity'] ?? 0);
        $chlorine = (float)($data['chlorine'] ?? 0);
        $biofilterRate = (float)($data['biofilter_rate'] ?? 20.0);


        $phStatus = $this->aguaModel->classifyParameter('ph', $ph);
        $turbidityStatus = $this->aguaModel->classifyParameter('turbidity', $turbidity);
        $chlorineStatus = $this->aguaModel->classifyParameter('chlorine', $chlorine);


        $filteredTurbidity = $this->aguaModel->simulateBiofilter($turbidity, $biofilterRate);
        $filteredTurbidityStatus = $this->aguaModel->classifyParameter('turbidity', $filteredTurbidity);

        $isPotableBefore = ($phStatus === 'Potável' && $turbidityStatus === 'Potável' && $chlorineStatus === 'Potável');
        $isPotableAfter = ($phStatus === 'Potável' && $filteredTurbidityStatus === 'Potável' && $chlorineStatus === 'Potável');

        return [
            'before' => [
                'ph' => ['value' => $ph, 'status' => $phStatus],
                'turbidity' => ['value' => $turbidity, 'status' => $turbidityStatus],
                'chlorine' => ['value' => $chlorine, 'status' => $chlorineStatus],
                'is_potable' => $isPotableBefore
            ],
            'after' => [
                'turbidity' => ['value' => $filteredTurbidity, 'status' => $filteredTurbidityStatus],
                'is_potable' => $isPotableAfter
            ]
        ];
    }
}