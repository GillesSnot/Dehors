<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ImportUserCsvType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/importUserCsv', name: 'app_admin_import_csv')]
    public function importUserCsv(Request $request): Response
    {

        $importUserCsvForm = $this->createForm(ImportUserCsvType::class);
        $importUserCsvForm->handleRequest($request);

        if($importUserCsvForm->isSubmitted() && $importUserCsvForm->isValid()) {
            $stream = fopen($importUserCsvForm->getData()['csv']->getPathname(), 'r');
            $users = [];
            fgetcsv($stream);
            while(!feof($stream))
            {
                $line = fgetcsv($stream, 0, ';');
                if (false !== $line) {
                    array_push($users, $line);
                }
            }
            foreach ($users as $userCsv) {
                $user = new User();
                $user->setPseudo($userCsv[0])
                    ->setNom($userCsv[1])
                    ->setPrenom($userCsv[2])
                    ->setEmail($userCsv[3])
                    ->setTelephone($userCsv[4])
                    ->setRoles(['ROLE_USER'])
                ;
                $this->em->persist($user);
            }
            $this->em->flush();
            $this->addFlash('success', count($users) . ' utilisateurs ajoutés');
            return $this->redirectToRoute('app_sortie');
            
        }
        return $this->render('admin/importUserCsv.html.twig', [
            'importUserCsvForm' => $importUserCsvForm,
        ]);
    }
}
