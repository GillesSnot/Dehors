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

    #[Route('/profil/id/{id}', name: 'app_profil')]
    public function index($id): Response
    {
        $user = $this->userRepository->find($id);
        return $this->render('profil/index.html.twig', [
            'user' => $user,
            'controller_name' => 'ProfilController'

        ]);
    }

    #[Route('/profil/update/', name: 'app_update_profil')]
public function update(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    $userForm = $this->createForm(UserType::class, $user);
    $userForm->handleRequest($request);

    if ($userForm->isSubmitted() && $userForm->isValid()) {
        // Handle password update
        if (!empty($userForm->get('password')->getData())) {
            $password = $userForm->get('password')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));
        }

        // Handle photo update
        if (!empty($userForm->get('photo')->getData())) {
            /** @var UploadedFile $photo */
            $photo = $userForm->get('photo')->getData();

            // Read the file content
            $imagedata = file_get_contents($photo->getPathname());

            // Convert image to base64
            $base64 = base64_encode($imagedata);

            // Create the full data URL with MIME type
            $mimeType = $photo->getMimeType();
            $src = 'data:' . $mimeType . ';base64,' . $base64;

            // Set the base64 image as photo in the User entity
            $user->setPhoto($src);
        }

        // Persist and flush changes to the database
        $entityManager->persist($user);
        $entityManager->flush();

        // Redirect after successful update
        return $this->redirectToRoute('app_update_profil');
    }

    // Render the update form
    return $this->render('profil/update.html.twig', [
        'userForm' => $userForm->createView(),
        'user' => $user,
        'photo' => stream_get_contents($user->getPhoto())
    ]);
}


}
