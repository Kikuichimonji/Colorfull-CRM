<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Planning;
use App\Entity\ContactType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    /**
     * Show the dashboard
     *
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     * 
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var ContactRepository $contactRepository  */
        $contactRepository = $doctrine->getRepository(Contact::class);
        $contactTypeRepository = $doctrine->getRepository(ContactType::class);

        //$contacts = $contactRepository->findAll();
        $query = $contactRepository->createQueryBuilder('c');
        $contacts = $query
        // ->select(['c','ct'])
        //     ->leftJoin('c.contactType','ct')
            ->setMaxResults(20)
            ->getQuery()
            ->execute();
            
        //dd($contacts[1]);
        $contactTypes = $contactTypeRepository->findAll();
        $planning = $this->getUser()->getPlanning();
        $error = is_null($planning) ? "Votre planing n'a pas été généré correctement" : null ;

        return $this->render('dashboard/index.html.twig', [
            'user' => $this->getUser(),
            'planning' => $planning,
            'contacts' => $contacts,
            'error' => $error,
            'contactTypes' => $contactTypes,
        ]);
    }
}
