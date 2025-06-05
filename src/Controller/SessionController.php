<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route("/session", name: "showSession")]
    public function show(SessionInterface $session): Response
    {
        $sessionData = $session->all();


        return $this->render('session.html.twig', [
            'sessionData' => $sessionData,
        ]);
    }

    #[Route("/session/delete", name: "deleteSession")]
    public function delete(SessionInterface $session): Response
    {
        $session->clear();

        $this->addFlash(
            'notice',
            'Nu är sessionen raderad'
        );

        return $this->redirectToRoute('showSession');
    }
}
