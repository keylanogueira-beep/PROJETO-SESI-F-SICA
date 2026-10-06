<?php

namespace Model;

class aguaQualidade
{
    public const POTAVEL = 'Potável';
    public const NAO_POTAVEL = 'Fora do padrão';

    private const LIMITES = [
        'ph'        => [6.0, 9.5],
        'turbidity' => [0.0, 5.0],
        'chlorine'  => [0.2, 2.0],
    ];

    public function classifyParameter(string $parameter, float $value): string
    {
        if (!isset(self::LIMITES[$parameter])) {
            throw new \InvalidArgumentException("Parâmetro desconhecido: {$parameter}");
        }

        [$min, $max] = self::LIMITES[$parameter];

        return ($value >= $min && $value <= $max) ? self::POTAVEL : self::NAO_POTAVEL;
    }

    public function simulateBiofilter(float $turbidity, float $removalRate): float
    {
        $removalRate = max(0.0, min(100.0, $removalRate));

        return round($turbidity * (1 - $removalRate / 100), 2);
    }
}
