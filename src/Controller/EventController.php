<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Entity\Rating;
use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\ParticipationRepository;
use App\Repository\RatingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\EventRepository;
use App\Repository\TagRepository;
use App\Entity\Event;
use App\Entity\EventTag;
use App\Entity\Tag;
use App\Form\EventType;


/**
 * IsGranted("ROLE_USER")
 */
class EventController extends AbstractController
{

    /**
     * @Route("/events", name="events", methods={"GET"})
     * 
     */
    public function index(EventRepository $eventRepo): Response
    {
        $events = $eventRepo->findAllOrderByCreatedAt();
        return $this->render('event/index.html.twig', ['events' => $events]);
    }

    /**
     * @Route("/events/fetch", name="fetch_events", methods={"GET"})
     */
    public function fetchEvents(EventRepository $eventRepo) : Response
    {
        $events = $eventRepo->findAllOrderByCreatedAt();

        return $this->json(['success'=>true, 'data'=>$events], 200, [], ['groups' => 'event:read']);
    }

    /**
     * @Route("/event", name="create_event", methods={"POST"})
     * @param Request $request
     * @param EventRepository $eventRepo
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function store(Request $request, TagRepository $tagRepo, ValidatorInterface $validator, SerializerInterface $serializer) : Response
    {
        // string json
        $jsonEvent = $request->getContent();

        // string json to object
        $event = $serializer->deserialize($jsonEvent, Event::class, 'json');
        $event->setCreatedAt(new \DateTime());
        $event->setUpdatedAt(new \DateTime());
        $user = $this->getUser();
        $event->setOwner($user);
        
        $errors = $validator->validate($event);
        
        if(count($errors) == 0){
            $eventManager = $this->getDoctrine()->getManager();

            // find default tag and set eventTag
            $tag = $tagRepo->findOneBy(array('tagName'=> 'All'));
            $eventTag = new EventTag();
            $eventTag->setTaggedEvent($event);
            $eventTag->setTag($tag);

            $eventManager->persist($event);
            $eventManager->persist($eventTag);
            $eventManager->flush();
            return $this->json(['success' => true, 'data'=>$event], 201, [], ['groups' => 'event:read']);
        }else{
            
            return $this->json(['success' => false, 'data'=>$errors], 400);
        }
    }

    /**
     * @Route("/event/{id}", name="event", methods={"GET"}, requirements={"id" = "\d+"})
     * @param EventRepository $eventRepo
     * @param ParticipationRepository
     * @return Response
     */
    public function show($id, EventRepository $eventRepo,   ParticipationRepository $participationRepo) : Response
    {
        
        $event = $eventRepo->findOneWithAll($id);
        
        /**
         * @var User $user
         */
        $user = $this->getUser();

        //if event exists
        if($event){
            //count participations grouped by type
            $goingList = $participationRepo->findByEventAndType($event->getId(), "Going");
            $interestedList = $participationRepo->findByEventAndType($event->getId(), "Interested");
            $likeList = $participationRepo->findByEventAndType($event->getId(), "Like");
            
             return $this->render('event/show.html.twig', [
                 'event' => [
                     'object' => $event,
                     'scoreRounded' => round($event->getScore()),
                     'ratingNumber' => count($event->getRatings()),
                     'goingList' => $goingList,
                     'interestedList' => $interestedList,
                     'likeList' => $likeList
                 ]
             ]);
        }
        return $this->redirectToRoute('events');
    }

    /**
     * @Route("/event/{id}/rating", name="event.rating", methods={"GET"}, requirements={"id" = "\d+"})
     * @param Request
     * @param Event
     * @param RatingRepository
     * @return Response
     */
    public function rating(Event $event, Request $request, RatingRepository $ratingRepo) : Response
    {
        $user = $this->getUser();
         //deal with rating score
         $rating_score = intVal($request->query->get("rating"));
         $entityManager = $this->getDoctrine()->getManager();

         if($rating_score!=null) {
             $rating = $ratingRepo->findRatingWithAll($user->getId(), $event->getId());
             //we want to update our rating of the event

             if(!$rating){
                 $rating = new Rating();
                 $rating->setCritic($user);
                 $rating->setCiriticSubject($event);
                 //ORM persist rating to both ManyToOne entities
                 $user->addRating($rating);
                 $event->addRating($rating);
             }
             
             $rating->setRatingScore($rating_score);

             //persist it first so that doctrine sets it's id
             $entityManager->persist($rating);

             $entityManager->flush();
         }

        return $this->redirectToRoute('event', ["id" => $event->getId()]);
    }

    /**
     * @Route("/event/edit/{id}", name="event.edit")
     * @param Event $event
     * @param Request $request
     * @return Response
     */
    public function edit(Event $event, Request $request): Response
    {
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
     * @Route("/event/delete/{id}", name="event.delete", methods={"GET"}, requirements={"id" = "\d+"})
     * @param Event
     * @param Request
     * @return Response
     */
    public function delete(Event $event) : Response
    {
        $user = $this->getUser();
        if($user->getId() === $event->getOwner()->getId()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
            
            return $this->redirectToRoute('events');
        }

        return $this->redirectToRoute("event", ['id' => $event->getId()]);
    }

    /**
     * @Route("/event/{id}/participate", name="event.participate", methods={"GET"})
     * @param Event $event
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @return Response
     */
    public function participate(Event $event, AuthenticationUtils $authenticationUtils, Request $request, ParticipationRepository $participationRepo): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        //deal with rating score
        $entityManager = $this->getDoctrine()->getManager();

        $participationType = (String)$request->query->get("participationType");

        if($participationType!=null) {

            $participationID = null;
            $participations = $participationRepo->findAll();

            //we check if rating had already been done or not
            foreach ($participations as $part) {

                //get participation id to be able to change it
                if ($part->getParticipantUser()->getId() == $user->getId() && $part->getParticipatedEvent()->getId() == $event->getId()) {
                    $participationID = $part->getId();
                }
            }


            //we want to update our rating of the event
            if ($participationID != null) {
                $participation = $participationRepo->find($participationID);
                $participation->setType($participationType);
                $entityManager->persist($participation);
            } else {
                $participation = new Participation();
                $participation->setParticipantUser($user);
                $participation->setParticipatedEvent($event);
                $participation->setType($participationType);
                $participation->setDate(date_create());

                //persist it first so that doctrine sets it's id
                $entityManager->persist($participation);

                //ORM persist rating to both ManyToOne entities
                $user->addParticipation($participation);
                $event->addParticipation($participation);
            }
        }

            $entityManager->flush();

            dump($participation,$user,$event);


        return $this->redirectToRoute('event', ['id' => $event->getId()]);
    }

    /**
     * @Route("/participation/{id}/delete/", name="event.participation.delete", methods={"GET"})
     * @param $id
     * @param Request $request
     * @param ParticipationRepository $participationRepo
     * @param EventRepository $eventRepo
     * @return Response
     */
    public function deleteParticipation($id, Request $request, ParticipationRepository $participationRepo, EventRepository $eventRepo): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $participation = $participationRepo->find($id);

        /**
         * @var User user
         */
        $user = $this->getUser();

        $event = $eventRepo->find($participation->getParticipatedEvent()->getId());

        $entityManager->remove($participation);

        $user->removeParticipation($participation);
        $event->removeParticipation($participation);

        $entityManager->flush();

        return $this->redirectToRoute('profile', ['id' => $user->getId()]);
    }

    /**
     * @Route("/events/filter", name="event_filter", methods={"GET"})
     */
    public function filter(Request $request, EventRepository $eventRepo, SerializerInterface $serializer) : Response
    {
        $query = $request->query->all();
       
        if(!empty($query)){        
            $events = $eventRepo->filterEvent($query);
        }else{
            $events = $events = $eventRepo->findAllOrderByCreatedAt();
        }

        return $this->json(['success' => true, 'count'=> count($events), 'data' => $events], 200, [], ['groups' => 'event:read']);
    }

    /**
     * @Route("/event/unpublish/{id}", name="unpublish_event", methods={"GET"}, requirements={"id" = "\d+"})
     */
    public function unpublish (Event $event) : Response
    {
        $user = $this->getUser();

        if($user->getId() == $event->getOwner()->getId()){
            $event->setState("Created");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
            return $this->json(['success' => true], 200);
        }

        return $this->json(['success' => false], 401);
    }

    /**
     * @Route("/event/publish/{id}", name="publish_event", methods={"GET"}, requirements={"id" = "\d+"})
     */
    public function publish (Event $event) : Response
    {
        $user = $this->getUser();

        if($user->getId() == $event->getOwner()->getId()){
            $event->setState("Published");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
            
            return $this->redirectToRoute('events');
        }

        return $this->redirectToRoute('event', ["id"=> $event->getId()]);
    }

}
