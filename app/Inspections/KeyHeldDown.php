<?php

namespace App\Inspections;

use Exception;

class KeyHeldDown
{

    /**
     * @param string $body
     *
     * @throws Exception
     */
    public function detect($body)
    {
        //Si hay más de 4 repeticiones de cualquier letra devuelve una excepción.
        if (preg_match('/(.)\\1{4,}/', $body)) {
            throw new Exception('Your reply contains spam');
        }
    }
}