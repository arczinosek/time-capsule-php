<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'home', methods: ['GET'])]
class HomeController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
