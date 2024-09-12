<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $password = $form->get('password')->getData();
            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));
            $user->setRoles(['ROLE_USER']);
            $base64=" data:image/jpeg;base64,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";
            $user->setPhoto($base64);
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_sortie');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
