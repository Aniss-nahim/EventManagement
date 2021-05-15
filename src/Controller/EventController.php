<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\EventRepository;
use App\Entity\Event;
use App\Form\EventType;


/**
 * @IsGranted("ROLE_USER")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     * 
     */
    public function index(EventRepository $eventRepo): Response
    {
        $events = $eventRepo->findAllOrderByCreatedAt();

        $event = new Event();

        return $this->render('event/index.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * @Route("/event", name="create_event", methods={"POST"})
     */
    public function store(Request $request, EventRepository $eventRepo, ValidatorInterface $validator, SerializerInterface $serializer) : Response
    {
        $jsonEvent = $request->getContent();

        $event = $serializer->deserialize($jsonEvent, Event::class, 'json');
        $event->setCreatedAt(new \DateTime());
        $event->setUpdatedAt(new \DateTime());
        $user = $this->getUser();    
        $event->setOwner($user);
        
        $errors = $validator->validate($event);
        
        if(count($errors) == 0){
            $eventManager = $this->getDoctrine()->getManager();
            $eventManager->persist($event);
            $eventManager->flush();
            return $this->json(['success' => true, 'data'=>$event], 201, [], ['groups' => 'event:read']);
        }else{
            
            return $this->json(['success' => false, 'data'=>$errors], 400);
        }
    }

    /**
     * @Route("/event/{id}", name="event", methods={"GET"}, requirements={"id" = "\d+"})
     **/
    public function show(int $id, EventRepository $eventRepo) : Response
    {
        // $event = $eventRepo->find($id);

        // if($event){
        //     return $this->render('event/show.html.twig', [
        //         'event' => $event
        //     ]);
        // }
        // return $this->redirectToRoute('events');
    }
}
