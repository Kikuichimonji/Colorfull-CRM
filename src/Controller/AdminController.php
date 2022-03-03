<?php

namespace App\Controller;

use App\Entity\ContactExtrafields;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function index(ManagerRegistry $doctrine): Response
    {
        $extrafieldsRepository = $doctrine->getRepository(ContactExtrafields::class);
        $extrafields = $extrafieldsRepository->findAll();

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/index.html.twig', [
            'user' => $this->getUser(),
            'extrafields' => $extrafields,
        ]);
    }
}
