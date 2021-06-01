<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Form\UserImageType;
use App\Repository\ParticipationRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/{id}", name="profile", requirements={"id" = "\d+"})
     * @param User $user
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function index(User $user, Request $request, EventRepository $eventRepo, ParticipationRepository $participationRepo, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ProfileType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $form = $this->createForm(ProfileType::class,$user);
        }

        $goingList = $participationRepo->findByUserAndType($user->getId(), "Going");
        $interestedList = $participationRepo->findByUserAndType($user->getId(), "Interested");
        $likeList = $participationRepo->findByUserAndType($user->getId(), "Like");
        $unpublishedEvents = $eventRepo->findUserUnpublishedEvents($user->getId());

        return $this->render('profile/index.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user,
            'goingList' => $goingList,
            'interestedList' => $interestedList,
            'likeList' => $likeList,
            'unpublishedEvents' => $unpublishedEvents
        ]);
    }

    /**
     * @Route("/profile/{id}/upload", name="profile.upload", methods={"POST"}, requirements={"id" = "\d+"})
     */
    public function upload(User $user, Request $request) : Response
    {
        $loggedUser = $this->getUser();
        $formImage = $this->createForm(UserImageType::class, $user);
        //dd($request);
        
        $formImage->handleRequest($request);
        
        if($formImage->isSubmitted()  && $loggedUser->getId() == $user->getId()){
            // Get the uploaded image
            $userImage = $request->files->get('user_image');

            if($userImage){
                $safeFileName = $user->getFirstName().'_'.$user->getLastName().'_'.$user->getId().'.'.$userImage->guessExtension();;

                try{
                    // move the image
                    $userImage->move(
                        $this->getParameter('profiles_directory'), //directory
                        $safeFileName // fileName
                    );

                    $entityManager = $this->getDoctrine()->getManager();
                    $user->setImage($safeFileName);
                    $entityManager->persist($user);
                    $entityManager->flush();

                }catch(FileException $e){

                }
            }

        }


        return $this->redirectToRoute('profile', ['id' => $user->getId()]);
    }
}
