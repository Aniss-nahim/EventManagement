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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
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
     * @param Event $event
     * @param Request $request
     * @param EventRepository $eventRepo
     * @return Response
     */
    public function show(Event $event, Request $request, RatingRepository $ratingRepo,  ParticipationRepository $participationRepo) : Response
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
            $entityManager = $this->getDoctrine()->getManager();

            if($rating_score!=null) {

                $rateID = null;
                $ratings = $ratingRepo->findAll();

                //we check if rating had already been done or not
                foreach($ratings as $rate){
                    if($rate->getCritic()->getId()==$user->getId() && $rate->getCiriticSubject()->getId()==$event->getId()){
                        $rateID = $rate->getId();
                    }
                }

                //we want to update our rating of the event
                if($rateID!=null){
                    $rating = $ratingRepo->find($rateID);
                    $rating->setRatingScore($rating_score);
                    $entityManager->persist($rating);
                }
                else{
                    $rating = new Rating();
                    $rating->setCritic($user);
                    $rating->setCiriticSubject($event);
                    $rating->setRatingScore($rating_score);

                    //persist it first so that doctrine sets it's id
                    $entityManager->persist($rating);

                    //ORM persist rating to both ManyToOne entities
                    $user->addRating($rating);
                    $event->addRating($rating);
                }

                $entityManager->flush();
                dump($rating);
            }

            dump($user,$event);

            //check if current user is owner of event
            if($event->getOwner()->getId()==$user->getId()){
                $owner = true;
            }

            //count participations

            $goingList = new ArrayCollection();
            $interestedList = new ArrayCollection();
            $likeList = new ArrayCollection();
            $participations = $participationRepo->findAll();
            foreach ($participations as $part){
                if($part->getParticipatedEvent()->getId()==$event->getId()) {
                    if ($part->getType() == "Going") {
                        $goingList->add($part);
                    }
                    if ($part->getType() == "Interested"){
                        $interestedList->add($part);
                    }
                    if($part->getType()=="Like"){
                        $likeList->add($part);
                    }
                }
            }

             return $this->render('event/show.html.twig', [
                 'event' => ['owner' => $owner,
                     'object' => $event,
                     'scoreRounded' => round($event->getScore()),
                     'ratingNumber' => count($event->getRatings()),
                     'goingNumber' => $goingList->count() ,
                     'interestedNumber' => $interestedList->count(),
                     'likeNumber' => $likeList->count(),
                     'goingList' => $goingList,
                     'interestedList' => $interestedList,
                     'likeList' => $likeList
                 ]
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
}
