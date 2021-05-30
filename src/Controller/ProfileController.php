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

        $goingList = $participationRepo->findByUserAndType($user->getId(), "Going");
        $interestedList = $participationRepo->findByUserAndType($user->getId(), "Interested");
        $likeList = $participationRepo->findByUserAndType($user->getId(), "Like");

        return $this->render('profile/index.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user,
            'goingList' => $goingList,
            'interestedList' => $interestedList,
            'likeList' => $likeList
        ]);
    }
}
