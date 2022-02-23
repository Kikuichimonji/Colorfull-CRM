<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\ContactExtrafields;
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

        $extrafieldsRepository = $entityManager->getRepository(ContactExtrafields::class);
        $extrafields = $extrafieldsRepository->findAll();

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
            "extrafields" => $extrafields,
        ]);
    }

    /**
     * Return the contact list in a json form
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * 
     * @return JsonResponse
     * 
     */
    public function eventDataFeed(ManagerRegistry $doctrine,Request $request): JsonResponse
    {

        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['userCreate','contacts','contactExtrafieldValues'],
        ]; //ignoring all the datas that goes too far in the relations
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);
        
        $entityManager = $doctrine->getManager();
        $contactRepository = $entityManager->getRepository(Contact::class);
        $query = $contactRepository->createQueryBuilder("c");
        $notNullQuerys = [];

        if(!is_null($request->get('query'))){
            if(!is_null($request->get('checkNames'))){
                $query
                ->orWhere('c.name LIKE :query');
            }
            if(!is_null($request->get('checkPhones'))){
                $query
                ->orWhere('c.phone2 LIKE :query')
                ->orWhere('c.phone1 LIKE :query');
                array_push($notNullQuerys,'c.phone2 IS NOT NULL OR c.phone1 IS NOT NULL');
            }
            if(!is_null($request->get('checkEmails'))){
                $query
                ->orWhere('c.email LIKE :query');
                array_push($notNullQuerys,'c.email IS NOT NULL');
            }
            if(!is_null($request->get('checkisCompany'))){
                $query->setParameter('isCompany',$request->get('checkisCompany') ? 1 : 0);
                array_push($notNullQuerys,'c.is_company = :isCompany');
            }
            foreach ($notNullQuerys as $singleQuery) { //Putting all the AND requirement at the end, cause the query builder parenthesis are annoying
                $query->andWhere($singleQuery);
            }
            if(empty($notNullQuerys) && is_null($request->get('checkNames'))){
                $query
                ->where('c.name LIKE :query')
                ->orWhere('c.name LIKE :query')
                ->orWhere('c.phone2 LIKE :query')
                ->orWhere('c.phone1 LIKE :query')
                ->orWhere('c.email LIKE :query');
            }   
            $query->setParameter('query','%'.$request->get('query').'%');

        }else{
            
            if(!is_null($request->get('checkPhones'))){
                $query
                ->andWhere('c.phone2 IS NOT NULL OR c.phone1 IS NOT NULL');
            }
            if(!is_null($request->get('checkEmails'))){
                $query
                ->andWhere('c.email IS NOT NULL');
            }
            if(!is_null($request->get('checkCompany'))){
                $query->andWhere('c.is_company = :isCompany');
                $query->setParameter('isCompany',$request->get('checkisCompany') ? 1 : 0);
            }
        }

        //return new JsonResponse($request->get('isCompany') ? 1 : 0);
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

    public function newContact(ManagerRegistry $doctrine,Request $request): Response
    {
        //dd($request->get('search'));
        $entityManager = $doctrine->getManager();
        $contactRepository = $entityManager->getRepository(Contact::class);

        $contactTypeRepository = $entityManager->getRepository(ContactType::class);
        $contactTypes = $contactTypeRepository->findAll();

        $manager = $doctrine->getManager();
        $post = $request->request;
        $contact = new Contact();
        $contact->setUserCreate($this->getUser());
        $contact->setName($post->get("name"));
        $contact->setIsCompany($post->get("isCompany") == "company");
        $contact->setPhone1($post->get("phone1"));
        $contact->setPhone2($post->get("phone2"));
        $contact->setEmail($post->get("email"));
        $contact->setCreatedAt(new \DateTime());

        $postContactType = [];
        $postList = $post = $request->request->all();
        foreach ($postList as $key => $value) {
            str_contains(explode('-',$key)[0], "checkbox") ? array_push($postContactType,explode('-',$key)[1]) : null ;
        }

        foreach ($contactTypes as $contactType) {
            if(in_array($contactType->getId(),$postContactType) ){
                $contact->addContactType($contactType);
            }
        }
        
        $manager->persist($contact);
        $manager->flush();
        return $this->render('contact/index.html.twig', [
            "user" => $this->getUser(),
            "contactTypes" => $contactTypes,
        ]);
    }
}
