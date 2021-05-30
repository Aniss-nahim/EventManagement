<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\ParticipationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/{id}", name="profile")
     * @param User $user
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function index(User $user, Request $request,ParticipationRepository $participationRepo, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ProfileType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $form = $this->createForm(ProfileType::class,$user);
        }

        //count participations

        $goingList = new ArrayCollection();
        $interestedList = new ArrayCollection();
        $likeList = new ArrayCollection();

        $participations = $participationRepo->findAll();
        
        foreach ($participations as $part){
            if($part->getParticipantUser()->getId()==$user->getId()) {
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

        return $this->render('profile/index.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user,
            'goingNumber' => $goingList->count() ,
            'interestedNumber' => $interestedList->count(),
            'likeNumber' => $likeList->count(),
            'goingList' => $goingList,
            'interestedList' => $interestedList,
            'likeList' => $likeList
        ]);
    }
}
