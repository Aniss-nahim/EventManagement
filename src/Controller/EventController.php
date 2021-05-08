<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\EventRepository;


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

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }
}
