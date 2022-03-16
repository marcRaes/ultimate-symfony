<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Psr\Log\LoggerInterface;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController
{
    /**
     * @Route("hello/{prenom<\w+>?World}", name="hello")
     */
    public function hello($prenom, LoggerInterface $logger, Calculator $calculator, Slugify $slugify)
    {
        dump($slugify->slugify("Hello World"));

        $logger->info("Mon message de log !");

        $tva = $calculator->calcul(100);

        dump($tva);

        return new Response("Hello $prenom");
    }
}
