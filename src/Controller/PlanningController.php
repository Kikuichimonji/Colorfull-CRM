<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('planning/index.html.twig', [
            "user" => $this->getUser(),
        ]);
    }
}
