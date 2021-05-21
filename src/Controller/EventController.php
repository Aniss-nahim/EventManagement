<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Entity\Rating;
use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
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
     * @param EventRepository $eventRepo
     * @return Response
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
     * @param Request $request
     * @param EventRepository $eventRepo
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @return Response
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
            $event->setState("Published");
            $eventManager->persist($event);
            $eventManager->flush();
            return $this->json(['success' => true, 'data'=>$event], 201, [], ['groups' => 'event:read']);
        }else{
            
            return $this->json(['success' => false, 'data'=>$errors], 400);
        }
    }



    /**
     * @Route("/event/{id}", name="event", methods={"GET"}, requirements={"id" = "\d+"})
     * @param Event $event
     * @param Request $request
     * @param EventRepository $eventRepo
     * @return Response
     */
    public function show(Event $event, Request $request, EventRepository $eventRepo) : Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /**
         * @var User $user
         */
        $user = $this->getUser();

        /**
         * @var boolean is true if the current user is the owner of the event
         */
        $owner = false;

        //if event exists
        if($event){

            //deal with rating score
            $rating_score = (int)$request->query->get("rating");

            if($rating_score!=null) {

                $rating = new Rating();
                $rating->setCritic($user);
                $rating->setCiriticSubject($event);
                $rating->setRatingScore($rating_score);


                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($rating);

                //ORM persist rating to both ManyToOne entities
                $user->addRating($rating);
                $event->addRating($rating);


                $entityManager->flush();
                dump($rating);
            }

            dump($user,$event);

            //check if current user is owner of event
            if($event->getOwner()->getId()==$user->getId()){
                $owner = true;
            }

             return $this->render('event/show.html.twig', [
                 'event' => ['owner' => $owner,
                     'object' => $event,
                     'scoreRounded' => round($event->getScore()),
                     'ratingNumber' => count($event->getRatings())]
             ]);
        }
        return $this->redirectToRoute('events');
    }

    /**
     * @Route("/event/edit/{id}", name="event.edit")
     * @param Event $event
     * @param Request $request
     * @return Response
     */
    public function edit(Event $event, Request $request): Response
    {
        dump($event);
        $form = $this->createForm(EventType::class,$event);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $form = $this->createForm(EventType::class,$event);
        }

        return $this->render('event/edit.html.twig', [
            'eventForm' => $form->createView(),
            'event' => $event
        ]);
    }

    /**
     * @Route("/event/{id}/participate", name="event.participate")
     * @param Event $event
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @return Response
     */
    public function participate(Event $event, AuthenticationUtils $authenticationUtils, Request $request): Response
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $participationType = (int)$request->query->get("participationType");
        if($participationType!=null) {

            $participation = new Participation();
            $participation->setParticipantUser($user);
            $participation->setParticipatedEvent($event);
            $participation->setType($participationType);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participation);

            //ORM persist participation to both ManyToOne entities
            $user->addParticipation($participation);
            $event->addParticipation($participation);


            $entityManager->flush();
            dump($participation,$user,$event);
        }

        $route = "event/".$event->getId();

        return $this->redirectToRoute($route);
    }
}
