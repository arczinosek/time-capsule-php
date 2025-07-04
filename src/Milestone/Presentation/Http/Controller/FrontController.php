<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'app_')]
class FrontController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/wall', name: 'wall', methods: ['GET'])]
    public function milestones(): Response
    {
        return $this->render('wall/index.html.twig');
    }
}
