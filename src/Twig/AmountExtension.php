<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('amount', [$this, 'amount'])
        ];
    }

    public function amount($price, string $symbol = '€', string $decsep = ',', string $thousandsep = ' ')
    {
        $price = number_format($price /= 100, 2, $decsep, $thousandsep);

        return $price . ' ' . $symbol;
    }
}
