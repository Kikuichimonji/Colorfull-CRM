<?php

namespace App\EventSubscriber;

use CalendarBundle\Entity\Event;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Event\CalendarEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar, ManagerRegistry $doctrine,Security $security)
    {
        
        /*$eventCollection = $security->getUser()->getPlanning()->getEvents();
        dd($eventCollection);
        foreach($eventCollection as $event){

            $planningEvent = new Event(
                $event->getLabel(),
                $event->getDateStart()->format("Y-m-d H:i:s"),
                $event->getDateEnd()->format("Y-m-d H:i:s")
            );

            $planningEvent->setOptions([
                'backgroundColor' => '#3788d8',
                'borderColor' => '#3788d8',
            ]);
            $planningEvent->addOption(
                'url',
                $this->router->generate('consult_show', [
                    'id' => $event->getId(),
                ])
            );

            $calendar->addEvent($planningEvent);
        }
            
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();*/

    }
}