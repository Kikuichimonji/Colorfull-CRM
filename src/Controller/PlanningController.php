<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Planning;
use App\Entity\EventType;
use App\Form\EventFormType;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


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
        $eventCollection = $this->getUser()->getPlanning()->getEvents();
        $events = [];
        foreach($eventCollection as $event){
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
        return $this->render('planning/index.html.twig', [
            "user" => $this->getUser(),
            "eventTypes" => $eventTypes,
            "events" => json_encode($events),
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
        $event = new Event($post);

        foreach($post as $key => $value){
            $method = "set".ucfirst($key);
            if(method_exists($event,$method)){
                if(($method == "setDateStart" || $method == "setDateEnd") && $value != "null"){
                    $value = new \DateTime($value);
                }
                if($method == "setEventType"){
                    $value = $eventTypeRepository->findOneBy(["id" => "$value"]);
                }
                if($method == "setPlanning"){
                    $value = $planningRepository->findOneBy(["id" => "$value"]);
                }
                $value = $value === "null" ? null : $value;
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

        $form = $this->createForm(EventFormType::class);
        $form->submit($request->request->all());
        
        if (!$form->isValid()) {
            $errors = $form->getErrors(true); // Array of Error
            return new JsonResponse($errors[0]->getMessage());
        }

        $post = $request->request->all();
        $eventRepository = $doctrine->getRepository(Event::class);
        $event = $eventRepository->find($request->request->get("id"));

        $eventTypeRepository = $doctrine->getRepository(EventType::class);
        $planningRepository = $doctrine->getRepository(Planning::class);
        $manager = $doctrine->getManager();

        foreach($post as $key => $value){
            $method = "set".ucfirst($key);
            if(method_exists($event,$method)){
                if($method == "setDateStart" || $method == "setDateEnd" && $value != "null"){
                    $value = new \DateTime($value);
                }
                if($method == "setEventType"){
                    $value = $eventTypeRepository->findOneBy(["id" => "$value"]);
                }
                if($method == "setPlanning"){
                    $value = $planningRepository->findOneBy(["id" => "$value"]);
                }
                $event->$method($value === "null" ? null : $value);
            }
        }

        $manager->flush();
        return new JsonResponse($event);
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
        $this->denyAccessUnlessGranted('ROLE_USER');
        if(!$this->isCsrfTokenValid("event_delete",$request->request->get("_token"))){
            return new JsonResponse("Problem CSRF");
        }
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Event::class);
        $event = $repository->find($request->request->get("id"));
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
}
