<?php

namespace App\Helpers;

class UnitConverter
{
    /**
     * Grupos de unidades compatibles entre sí.
     * Cada unidad mapea a su factor de conversión respecto a la unidad base del grupo.
     */
    protected static array $groups = [
        'weight' => [
            'base' => 'g',
            'units' => [
                'kg' => 1000,
                'g'  => 1,
            ],
        ],
        'volume' => [
            'base' => 'ml',
            'units' => [
                'litro'       => 1000,
                'ml'          => 1,
                'cucharada'   => 15,
                'cucharadita' => 5,
            ],
        ],
        'count' => [
            'base' => 'unidad',
            'units' => [
                'docena' => 12,
                'unidad' => 1,
            ],
        ],
    ];

    /**
     * Devuelve el nombre del grupo al que pertenece una unidad (weight, volume, count).
     * Devuelve null si la unidad no está reconocida.
     */
    public static function groupOf(string $unit): ?string
    {
        foreach (self::$groups as $groupName => $group) {
            if (array_key_exists($unit, $group['units'])) {
                return $groupName;
            }
        }
        return null;
    }

    /**
     * Devuelve las unidades válidas (compatibles) para una unidad dada.
     * Ej: compatibleUnits('kg') -> ['kg', 'g']
     */
    public static function compatibleUnits(string $unit): array
    {
        $group = self::groupOf($unit);
        if (!$group) {
            return [$unit]; // si no se reconoce, solo permite la misma
        }
        return array_keys(self::$groups[$group]['units']);
    }

    /**
     * Verifica si dos unidades son del mismo grupo (intercambiables).
     */
    public static function areCompatible(string $unitA, string $unitB): bool
    {
        $groupA = self::groupOf($unitA);
        $groupB = self::groupOf($unitB);
        return $groupA !== null && $groupA === $groupB;
    }

    /**
     * Convierte una cantidad desde una unidad a otra, dentro del mismo grupo.
     * Lanza excepción si las unidades no son compatibles.
     */
    public static function convert(float $quantity, string $fromUnit, string $toUnit): float
    {
        if ($fromUnit === $toUnit) {
            return $quantity;
        }

        $group = self::groupOf($fromUnit);

        if (!$group || !self::areCompatible($fromUnit, $toUnit)) {
            throw new \InvalidArgumentException("No se puede convertir de '{$fromUnit}' a '{$toUnit}': unidades incompatibles.");
        }

        $units = self::$groups[$group]['units'];

        // Convertimos a la unidad base del grupo, y de ahí a la unidad destino
        $quantityInBase = $quantity * $units[$fromUnit];
        return $quantityInBase / $units[$toUnit];
    }
}