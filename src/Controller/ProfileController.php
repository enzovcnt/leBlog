<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Profile;
use App\Form\ImageForm;
use App\Form\ProfileForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function profile(EntityManagerInterface $manager, Request $request, Profile $profile): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();

        $form = $this->createForm(ProfileForm::class, $profile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($profile);
            $manager->flush();
            return $this->redirectToRoute('app_profile', ['id' => $user->getProfile()->getId()]);
        }
        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'profile' => $profile,
            'user' => $user,
        ]);
    }


    #[Route('/profile/addImage/{id}', name: 'app_addImageProfile')]
    public function addImageProfile( EntityManagerInterface $manager, Request $request, UserRepository $userRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $image = new Image();
        $form = $this->createForm(ImageForm::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setProfile($this->getUser()->getProfile());

            $manager->persist($image);
            $manager->flush();

            return $this->redirectToRoute('app_addImageProfile', ['id' => $user->getProfile()->getId()]);
        }

        return $this->render('profile/addImage.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
