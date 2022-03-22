<?php

namespace App\Taxes;

class Detector
{
    protected $seuil;

    public function __construct(float $seuil)
    {
        $this->seuil = $seuil;
    }

    public function detect(float $amount): bool
    {
        if ($amount > $this->seuil) {
            return true;
        }

        return false;
    }
}
