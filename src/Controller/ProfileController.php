<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
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
    public function index(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ProfileType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $form = $this->createForm(ProfileType::class,$user);
        }

        return $this->render('profile/index.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user
        ]);
    }
}
