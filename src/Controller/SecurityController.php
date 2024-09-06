<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\TokenRepository;
use App\Form\PasswordType;
use App\Form\EmailType;
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
        private readonly TokenRepository $tokenRepository,
        private readonly EntityManagerInterface $entityManager,
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
    #[Route(path: '/generateToken', name: 'app_generate_password_reset')]
    public function generatePasswordReset(Request $request,UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $userForm = $this->createForm(EmailType::class, $user);
        $userForm->handleRequest($request);
        $url='';

        if($userForm->isSubmitted() && $userForm->isValid()) {
            $user= $this->userRepository->findOneBy(['email'=>$userForm->get('email')->getData()]);

           if($user){
                $url=generateToken($length = 64);
                if($this->tokenRepository->findOneBy(['User' => $user])){
                    $token=$this->tokenRepository->findOneBy(['User' => $user]);
                }
                else{
                    $token = new Token();
                }
                $token->setToken($url);
                $token->setUser($user);
                // Persister le token dans la base de données
                $entityManager->persist($token);
                $entityManager->flush();
                return $this->redirect(getUrlOrigin().'/resetPassword/'.$url);
            }
            else{
                $this->addFlash('error', 'Aucun compte existant pour l\'adresse mail : '.$userForm->get('email')->getData());
            }
        }
        return $this->render('security/generateToken.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }
    #[Route(path: '/resetPassword/{token}', name: 'app_reset_password')]
    public function resetPassword($token,Request $request,UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // $user= $this->userRepository->findOneBy(['token'=>$userForm->get('token')->getData()]);3
        $user= $this->tokenRepository->findOneBy(['token'=>$token])->getUser();
        $userForm = $this->createForm(PasswordType::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()) {
            if(!empty($userForm->get('password')->getData())){
                $password=$userForm->get('password')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $password));
            }
            // Format the image SRC:  data:{mime};base64,{data};
            $entityManager->persist($user);
            $entityManager->flush();

            $existingToken = $this->tokenRepository->findOneBy(['token'=>$token]);

            // Si un token existe déjà, on le supprime
            if ($existingToken) {
                $this->entityManager->remove($existingToken);
                $this->entityManager->flush();  // Nécessaire pour supprimer immédiatement l'ancien token
            }
            
            return $this->redirectToRoute('app_sortie');
        }
        return $this->render('security/resetPassword.html.twig', [
            'userForm' => $userForm->createView(),
        ]);       
    }
}

function generateToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

function getUrlOrigin() {
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $scheme . '://' . $host;
}