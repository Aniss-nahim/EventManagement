<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Form\UserType;

class RegisterController extends AbstractController
{
   /** 
     * @Route("/register", name="registerView", methods={"GET"})
     */
    public function create() : Response
    {
        $user = new User();
        // create the form
        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('register'),
            'method' => 'POST'
        ]);

        return $this->render('register/index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function store(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setRegistrationDate(new \DateTime());
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login', ['last_username' => $user->getEmail()]);
        }
        
        return $this->render('register/index.html.twig', ['form' => $form->createView()]);
    }
}
