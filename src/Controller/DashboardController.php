<?php

namespace App\Controller;

use App\Entity\Planning;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $repository = $doctrine->getRepository(Planning::class);
        //$planning = $repository->findOneBy(["planning_owner" => $this->getUser()->getId()]);
        $planning = $this->getUser()->getPlanning();
        $error = is_null($planning) ? "Votre planing n'a pas été généré correctement" : null ;
        //dd($planning->getEvents());
        return $this->render('dashboard/index.html.twig', [
            'user' => $this->getUser(),
            'planning' => $planning,
            'error' => $error,
        ]);
    }
}
