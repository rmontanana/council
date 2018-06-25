<?php

namespace App\Inspections;

class Spam
{
    /**
     * All regustered inspections
     *
     * @var array
     */
    protected $inspections = [
            InvalidKeywords::class,
            KeyHeldDown::class
        ];

    /**
     * @param string $body
     *
     * @return bool
     */
    public function detect($body)
    {
        foreach ($this->inspections as $inspection) {
            app($inspection)->detect($body);
        }

        return false;
    }
}