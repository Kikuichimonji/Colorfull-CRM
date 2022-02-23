<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\ContactType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class ContactController extends AbstractController
{
    /**
     * Show the contact page depending on the search parameters
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * 
     * @return Response
     * 
     */
    public function index(ManagerRegistry $doctrine,Request $request): Response
    {
        //dd($request->get('search'));
        $entityManager = $doctrine->getManager();
        $contactRepository = $entityManager->getRepository(Contact::class);

        $contactTypeRepository = $entityManager->getRepository(ContactType::class);
        $contactTypes = $contactTypeRepository->findAll();

        $query = $contactRepository->createQueryBuilder("c");

        if(!is_null($request->get('search'))){
            $query
            ->where('c.name LIKE :search')
            ->setParameter('search','%'.$request->get('search').'%');
        }
        $contacts = $query
        ->getQuery()
        ->execute();

        return $this->render('contact/index.html.twig', [
            "user" => $this->getUser(),
            "contactTypes" => $contactTypes,
        ]);
    }

    public function eventDataFeed(ManagerRegistry $doctrine,Request $request): Response
    {

        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['userCreate','contacts','contactExtrafieldValues'],
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);
        
        $entityManager = $doctrine->getManager();
        $contactRepository = $entityManager->getRepository(Contact::class);
        $query = $contactRepository->createQueryBuilder("c");

        if(!is_null($request->get('query'))){
            $query
            ->where('c.name LIKE :search')
            ->setParameter('search','%'.$request->get('query').'%');
        }
        $contacts = $query
        ->getQuery()
        ->execute();

        $jsonContacts = [];
        foreach ($contacts as $contact) {
            $json = $serializer->serialize($contact, 'json');
            array_push($jsonContacts,$json);
        }

        return new JsonResponse($jsonContacts);
    }
}
