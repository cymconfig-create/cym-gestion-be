<?php

namespace App\Repositories;

use App\Models\Selector;

class SelectorRepository extends Repository
{

    /**
     * Recupera todos los selectores y los organiza en una estructura jerárquica
     * basada en sus relaciones 'code' y 'dad_selector_code'.
     *
     * @return array
     */
    function all(): array
    {
        // Obtener todos los selectores de la base de datos
        $selectors = Selector::where('status', 1)->get();
        $fathers = [];

        // Primero, iterar para encontrar todos los selectores padre
        foreach ($selectors as $item) {
            if ($item->dad_selector_code === null) {
                $father = (object) [
                    "selector_id" => $item->selector_id,
                    "code" => $item->code,
                    "name" => $item->name,
                    "order" => $item->order,
                    "options" => []
                ];
                $fathers[] = $father;
            }
        }

        // Segundo, iterar de nuevo para asignar los hijos a sus respectivos padres
        foreach ($selectors as $item) {
            foreach ($fathers as $father) {
                // Verificar si el código del padre del elemento coincide con el código del padre
                if ($item->dad_selector_code === $father->code) {
                    $option = (object) [
                        "selector_id" => $item->selector_id,
                        "name" => $item->name,
                        "code" => $item->code,
                        "order" => $item->order
                    ];
                    $father->options[] = $option;
                }
            }
        }

        // Ordenar las opciones de los hijos para cada padre por el campo 'order'
        foreach ($fathers as $father) {
            usort($father->options, function ($a, $b) {
                return $a->order <=> $b->order;
            });
        }

        // Finalmente, ordenar la lista principal de padres por su campo 'code'
        usort($fathers, function ($a, $b) {
            return $a->code <=> $b->code;
        });

        return $fathers;
    }
}
