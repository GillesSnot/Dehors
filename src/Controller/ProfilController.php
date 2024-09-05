<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    #[Route('/profil/{id}', name: 'app_profil')]
    public function index($id): Response
    {
        $user = $this->userRepository->find($id);
        return $this->render('profil/index.html.twig', [
            'user' => $user,
            'controller_name' => 'ProfilController'

        ]);
    }

    #[Route('/profil/update', name: 'app_update_profil')]
    public function update(Request $request,UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()) {
            if(!empty($userForm->get('password')->getData())){
                $password=$userForm->get('password')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $password));
            }
            if(!empty($userForm->get('photo')->getData())){
                $photo=$userForm->get('photo')->getData();
                $imagedata = file_get_contents($photo);
                $base64 = base64_encode($imagedata);
                $src = 'data: '. $photo->guessExtension().';base64,'.$base64;
                $user->setPhoto($src);
            }
            

            // Format the image SRC:  data:{mime};base64,{data};
           
            
            
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_update_profil');
        }
        return $this->render('profil/update.html.twig', [
            'userForm' => $userForm->createView(),
            'user' => $user
        ]);
    }


}
