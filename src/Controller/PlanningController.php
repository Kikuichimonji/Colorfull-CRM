<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Planning;
use App\Entity\EventType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlanningController extends AbstractController
{
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $eventTypeRepository = $doctrine->getRepository(EventType::class);
        $eventType = $eventTypeRepository->findAll();
        $eventCollection = $this->getUser()->getPlanning()->getEvents();
        $events = [];
        foreach($eventCollection as $event){
            array_push($events, [
                "id" => $event->getId(),
                "backgroundColor" => $event->getEventType()->getColor(),
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
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('planning/index.html.twig', [
            "user" => $this->getUser(),
            "eventTypes" => $eventType,
            "events" => json_encode($events),
        ]);
    }

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
        $manager->persist($event);
        $manager->flush();
        return new JsonResponse($event->getEventType());
    }

    public function updateEvent(Request $request,ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
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
}
