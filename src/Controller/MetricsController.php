<?php

namespace App\Controller;

# use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
# use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MetricsController extends AbstractController
{
    #[Route('/metrics', name: 'metrics')]
    public function metrics(): Response
    {
        return $this->render('metrics.html.twig', [
            'controller_name' => 'LibraryController',
        ]);
    }
}
