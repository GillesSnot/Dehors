<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Form\PasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // logout the user in on the current firewall
        $response = $security->logout();
        
        // you can also disable the csrf logout
        $response = $security->logout(false);
        $response = new Response();
        $response->headers->clearCookie('REMEMBERME');
        $response->send();
    }
    #[Route(path: '/generateToken/{id}', name: 'app_generate_password_reset')]
    public function generatePasswordReset($id,Request $request,UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManage): Response
    {
        $user = $this->getUser();
        $userForm = $this->createForm(PseudoType::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()) {
            $password=$userForm->get('password')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('security/generateToken.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
