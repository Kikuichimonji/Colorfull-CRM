<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\ContactType;
use App\Entity\ContactExtrafields;
use App\Entity\ContactExtrafieldValue;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class ContactController extends AbstractController
{
    /**
     * Show the contact page
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * 
     * @return Response Returning contactTypes and extrafields
     * 
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager = $doctrine->getManager();

        $contactTypeRepository = $entityManager->getRepository(ContactType::class);
        $contactTypes = $contactTypeRepository->findAll();

        $extrafieldsRepository = $entityManager->getRepository(ContactExtrafields::class);
        $extrafields = $extrafieldsRepository->findAll();

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
    public function eventDataFeed(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $encoder = new JsonEncoder();
        $defaultContext = [ //ignoring all the datas that goes too far in the relations to limit depth
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['userCreate', 'contacts', 'contactExtrafieldValues'],
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);

        $entityManager = $doctrine->getManager();
        $contactRepository = $entityManager->getRepository(Contact::class);
        $query = $contactRepository->createQueryBuilder("c");
        $notNullQuerys = [];

        if (!is_null($request->get('query'))) { //if the user enter a search query
            if (!is_null($request->get('checkNames'))) { //all the checkbox verifications
                $query
                    ->orWhere('c.name LIKE :query');
            }
            if (!is_null($request->get('checkPhones'))) {
                $query
                    ->orWhere('c.phone2 LIKE :query')
                    ->orWhere('c.phone1 LIKE :query');
                array_push($notNullQuerys, 'c.phone2 IS NOT NULL OR c.phone1 IS NOT NULL');
            }
            if (!is_null($request->get('checkEmails'))) {
                $query
                    ->orWhere('c.email LIKE :query');
                array_push($notNullQuerys, 'c.email IS NOT NULL');
            }
            if (!is_null($request->get('checkisCompany'))) {
                $query->setParameter('isCompany', $request->get('checkisCompany') ? 1 : 0);
                array_push($notNullQuerys, 'c.is_company = :isCompany');
            }
            foreach ($notNullQuerys as $singleQuery) { //Putting all the AND requirement at the end, cause the query builder parenthesis are annoying
                $query->andWhere($singleQuery);
            }
            if (empty($notNullQuerys) && is_null($request->get('checkNames'))) { // if the user search with everything checked out
                $query
                    ->where('c.name LIKE :query')
                    ->orWhere('c.name LIKE :query')
                    ->orWhere('c.phone2 LIKE :query')
                    ->orWhere('c.phone1 LIKE :query')
                    ->orWhere('c.email LIKE :query');
            }
            $query->setParameter('query', '%' . $request->get('query') . '%');
        } else { //if we do not have a search query in the parameters

            if (!is_null($request->get('checkPhones'))) {
                $query
                    ->andWhere('c.phone2 IS NOT NULL OR c.phone1 IS NOT NULL');
            }
            if (!is_null($request->get('checkEmails'))) {
                $query
                    ->andWhere('c.email IS NOT NULL');
            }
            if (!is_null($request->get('checkCompany'))) {
                $query->andWhere('c.is_company = :isCompany');
                $query->setParameter('isCompany', $request->get('checkisCompany') ? 1 : 0);
            }
        }

        $contacts = $query
            ->getQuery()
            ->execute();

        $jsonContacts = [];
        foreach ($contacts as $contact) {
            $json = $serializer->serialize($contact, 'json'); //transforming the contact into a json object
            array_push($jsonContacts, $json);
        }

        return new JsonResponse($jsonContacts); //we send back a json with the events so we can use them in JS
    }

    /**
     * Create a new contact and save the extrafields
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * 
     * @return Response
     * 
     */
    public function newContact(ManagerRegistry $doctrine, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager = $doctrine->getManager();

        $contactTypeRepository = $entityManager->getRepository(ContactType::class);
        $contactTypes = $contactTypeRepository->findAll();

        $contactExtrafieldsRepository = $entityManager->getRepository(ContactExtrafields::class);

        $extrafieldsRepository = $entityManager->getRepository(ContactExtrafields::class);
        $extrafields = $extrafieldsRepository->findAll();

        $post = $request->request;

        $contact = new Contact(); //Setting up all the contact data the barbarian way
        $contact->setUserCreate($this->getUser());
        $contact->setName($post->get("name"));
        $contact->setIsCompany($post->get("isCompany") == "company");
        $contact->setPhone1($post->get("phone1"));
        $contact->setPhone2($post->get("phone2"));
        $contact->setEmail($post->get("email"));
        $contact->setCreatedAt(new \DateTime());

        $extraArray = []; //we're gonna store all the extarfields values in it
        $postContactType = []; //we're gonna store the contact types in it
        $postList = $post = $request->request->all();
        foreach ($postList as $key => $value) { //Reading all the checkboxes datas and extravalues and pushing them into thir array to add them later
            str_contains(explode('-', $key)[0], "checkbox") ? array_push($postContactType, explode('-', $key)[1]) : null;
            str_contains(explode('_', $key)[0], "EX") ? array_push($extraArray, [explode('_', $key)[1], $value]) : null;
        }

        foreach ($contactTypes as $contactType) { //Reading all the contactTypes, and if they matches the array we add them in the collection
            if (in_array($contactType->getId(), $postContactType)) {
                $contact->addContactType($contactType);
            }
        }

        foreach ($extraArray as $extra) { 
            $extrafield = $contactExtrafieldsRepository->findOneBy(["id" => $extra[0]]); //we get the extrafield with the corresponding id
            $extrafieldValue = new ContactExtrafieldValue();
            $extrafieldValue->setContactExtrafield($extrafield);
            $extrafieldValue->setContact($contact);
            $extrafieldValue->setValue($extra[1]);
            $entityManager->persist($extrafieldValue);
        }

        $entityManager->persist($contact);
        $entityManager->flush();

        return $this->render('contact/index.html.twig', [
            "user" => $this->getUser(),
            "contactTypes" => $contactTypes,
            "extrafields" => $extrafields,
        ]);
    }

    /**
     * Show a single contact detail
     *
     * @param ManagerRegistry $doctrine
     * @param mixed $id
     * 
     * @return Response
     * 
     */
    public function showContact(ManagerRegistry $doctrine, $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager = $doctrine->getManager();
        $contactRepository = $entityManager->getRepository(Contact::class);
        $contact = $contactRepository->findOneBy(['id' => $id]);

        $contactTypeRepository = $entityManager->getRepository(ContactType::class);
        $contactTypes = $contactTypeRepository->findAll();

        $extrafieldsValueRepository = $entityManager->getRepository(ContactExtrafieldValue::class);

        $extrafieldsRepository = $entityManager->getRepository(ContactExtrafields::class);
        $extrafields = $extrafieldsRepository->findAll();

        return $this->render('contact/show.html.twig', [
            "user" => $this->getUser(),
            "contactTypes" => $contactTypes,
            "extrafields" => $extrafields,
            "contact" => $contact,
        ]);
    }

    /**
     * Update the datas from the contact details
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param mixed $id
     * 
     * @return Response
     * 
     */
    public function updateContact(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');
        $entityManager = $doctrine->getManager();
        $contactRepository = $entityManager->getRepository(Contact::class);
        $contact = $contactRepository->findOneBy(['id' => $id]);
        $post = $request->request;

        $contact->setName($post->get("name")); //setting up all the new datas
        $contact->setIsCompany($post->get("isCompany") == "company");
        $contact->setPhone1($post->get("phone1"));
        $contact->setPhone2($post->get("phone2"));
        $contact->setEmail($post->get("email"));

        foreach ($contact->getContactExtrafieldValues() as $extrafield) { //double foreach to store all the extrafields datas
            $extraId = $extrafield->getContactExtrafield()->getId();
            if (array_key_exists("EX_" . $extraId, $post->all())) {
                foreach ($contact->getContactExtrafieldValues() as $extrafieldValues) {
                    if ($extraId == $extrafieldValues->getContactExtrafield()->getId()) {
                        $extrafieldValues->setValue($post->get("EX_" . $extraId));
                    }
                }
            }
        }

        $entityManager->flush();
        return $this->redirectToRoute('contact-index');
    }
}
