<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Planning;
use App\Entity\EventType;
use App\Form\EventFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use function PHPUnit\Framework\isNull;

class PlanningController extends AbstractController
{
    /**
     * Show Planning page
     *
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     * 
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $eventTypeRepository = $doctrine->getRepository(EventType::class);
        $eventTypes = $eventTypeRepository->findAll();

        return $this->render('planning/index.html.twig', [
            "user" => $this->getUser(),
            "eventTypes" => $eventTypes,
        ]);
    }

    /**
     * Create a new event
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     * 
     */
    public function createEvent(Request $request,ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $eventTypeRepository = $doctrine->getRepository(EventType::class);
        $planningRepository = $doctrine->getRepository(Planning::class);
        $manager = $doctrine->getManager();
        $post = $request->request->all();
        $event = new Event();

        foreach($post as $key => $value){ //we create a new event from the form datas
            $method = "set".ucfirst($key);
            if(method_exists($event,$method)){ //we check if the setter actually exist
                if(($method == "setDateStart" || $method == "setDateEnd") && $value != "null"){
                    $value = new \DateTime($value);
                }
                if($method == "setEventType"){
                    $value = $eventTypeRepository->findOneBy(["id" => "$value"]);
                }
                if($method == "setPlanning"){
                    $value = $planningRepository->findOneBy(["id" => "$value"]);
                }
                $value = $value === "null" ? null : $value; //when we get the value, NULL and UNDEFINED are in a string form
                $value = $value === "undefined" ? null : $value;
            
                $event->$method($value);
            }
        }
        $manager->persist($event);
        $manager->flush();
        
        return new JsonResponse($event->getEventType());
    }

    /**
     * Update an event with new datas
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     * 
     */
    public function updateEvent(Request $request,ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $post = $request->request->all();
        $form = $this->createForm(EventFormType::class);
        $form->submit($post);
        
        if (!$form->isValid()) { //Form validation
            $errors = $form->getErrors(true); // Array of Error
            return new JsonResponse($errors[0]->getMessage());
        }


        $event = $doctrine->getRepository(Event::class)->find($request->request->get("id"));

        $eventTypeRepository = $doctrine->getRepository(EventType::class);
        $planningRepository = $doctrine->getRepository(Planning::class);
        $manager = $doctrine->getManager();

        foreach($post as $key => $value){
            $method = "set".ucfirst($key);
            if(method_exists($event,$method)){ //we check if the setter actually exist
                if($method == "setDateStart" || $method == "setDateEnd" && $value != "null"){
                    $value = new \DateTime($value);
                }
                if($method == "setEventType"){
                    $value = $eventTypeRepository->findOneBy(["id" => "$value"]);
                }
                if($method == "setPlanning"){
                    $value = $planningRepository->findOneBy(["id" => "$value"]);
                }
                if($method == "setIsImportant"){
                    $value = $value == 'true' ? 1 : 0;
                }
                $value = $value === "undefined" ? null : $value;
                $event->$method($value === "null" ? null : $value);

            }
        }
        
        $manager->flush();
        return new JsonResponse("ok");
    }
    /**
     * Delete an event from database
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     * 
     */
    public function deleteEvent(Request $request,ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER'); //if the user do not have the basic role he gets redirect to the login
        if(!$this->isCsrfTokenValid("event_delete",$request->request->get("_token"))){ //we verify if the token is valid
            return new JsonResponse("Problem CSRF");
        }
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Event::class);
        $event = $repository->find($request->request->get("id")); // we check if an event actually exist at this id
        if (!$event) {
            return new JsonResponse("Pas d'évenement trouvé à cet id : ".$request->request->get("id"));    
        }else{
            if($event->getPlanning()->getPlanningOwner()->getId() != $this->getUser()->getId()){
                return new JsonResponse("Vous n'avez pas le droit de modifier ce planning");
            }
            $entityManager->remove($event);
            $entityManager->flush();
            return new JsonResponse("Event deleted");
        }

    }

    /**
     * Send a json with all the events from a user ID
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param $id //user id
     * @return Response
     * @throws \Exception
     */
    public function eventFeed(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $doctrine->getRepository(Planning::class)->findOneBy(['id' => $id])->getPlanningOwner();
        $user = $user ?? $this->getUser();
        $eventCollection = $user ->getPlanning()->getEvents();
        $events = [];
        
        foreach($eventCollection as $event){ //for each events we get, we transform it into an array with some extra infos
            $dateParam = new \DateTime($request->query->get('start'));
            $dateEvent = new \Datetime($event->getDateStart()->format("Y-m-d H:i:s"));
            
            if(!date_diff($dateParam,$dateEvent)->invert){
                array_push($events, [
                    "id" => $event->getId(),
                    "backgroundColor" => $event->getColor() ?? $event->getEventType()->getColor(),
                    "start" => $event->getDateStart() ? $event->getDateStart()->format("Y-m-d H:i:s") : null,
                    "end" => $event->getDateEnd() ? $event->getDateEnd()->format("Y-m-d H:i:s") : null,
                    "title" => $event->getLabel(),
                    "description" => $event->getDescription(),
                    "isImportant" => $event->getIsImportant(),
                    "customColor" => $event->getColor(),
                    "eventType" => $event->getEventType()->getId(),
                    "planning" => $event->getPlanning()->getId(),
                ]);
            }
            
        }
        return new JsonResponse($events);
    }

    /**
     * Show a user planning only if the one looking have the right to access it
     *
     * @param ManagerRegistry $doctrine
     * @param $id
     * @return Response
     */
    public function show(ManagerRegistry $doctrine, $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $eventTypeRepository = $doctrine->getRepository(EventType::class);
        $eventTypes = $eventTypeRepository->findAll();

        $user = $doctrine->getRepository(Planning::class)->findOneBy(['id' => $id])->getPlanningOwner();
        if(!$user){
            return $this->render('planning/index.html.twig', [
                "user" => $this->getUser(),
                "eventTypes" => $eventTypes,
            ]);
        }
        if(!in_array($user->getPlanning(),$this->getUser()->getPlannings()->toArray())){
            $this->addFlash('error', "Vous n'avez pas accès à ce planning");
            return $this->render('planning/index.html.twig', [
                "user" => $this->getUser(),
                "eventTypes" => $eventTypes,
            ]);
        }
        return $this->render('planning/index.html.twig', [
            "user" => $user,
            "eventTypes" => $eventTypes,
        ]);
    }
}
