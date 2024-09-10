<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\CampusFilterType;
use App\Repository\UserRepository;
use App\Form\CampusType;
use App\Form\ImportUserCsvType;
use App\Form\VillesFilterType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $userRepository,
        private readonly CampusRepository $campusRepo,
        private readonly VilleRepository $villeRepo,
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

    #[Route('/listUser', name: 'app_admin_list_user')]
    public function listUser(Request $request): Response
    {  
        $listUser = $this->userRepository->findAll();
        $listUser = array_filter($listUser, function($user) {
            return $user->getRoles() === ['ROLE_USER'];  // Vérifie si l'utilisateur a exactement ['ROLE_USER']
        });
       
        return $this->render('admin/listUser.html.twig', [
            'users'=> $listUser
        ]);
    }

    #[Route('/setDesactivate/{id}', name: 'app_admin_set_descativate')]
    public function setDesactivate($id,Request $request): Response
    {  
        $user = $this->userRepository->find($id);
        $bool=false;
        if($user->isInactif()==true){
            $user->setInactif(false);
        }
        else {
            $user->setInactif(true);
            $bool=true;
        }
        $this->em->persist($user);
        $this->em->flush();

        $sorties = $user->getSortiesOrganisees();

        foreach ($sorties as $sortie) {
            $sortie->setAnnulation($bool);
            $description = $sortie->getDescription();
            $motif=' - compte désactivé';
            if($bool){
                $sortie->setDescription($description . $motif );
            }
            else{
                $sortie->setDescription(str_replace($motif,'',$description));
            }   
            $this->em->persist($sortie);
            $this->em->flush();
        }
       
        $sorties =$user->getInscriptionSortie();
        foreach ($sorties as $sortie) {
            if(!($user->getId()==$sortie->getOrganisateur()->getId())){
                $sortie->removeParticipant($user);
                $this->em->persist($sortie);
                $this->em->flush();
            }
        }
        
        return $this->redirectToRoute('app_admin_list_user');
    }

    #[Route('/delete/{id}', name: 'app_admin_delete_account')]
    public function deleteAccount($id,Request $request): Response
    {  
        $user = $this->userRepository->find($id);
        $sorties = $user->getSortiesOrganisees();
        // dd( $user);
        foreach ($sorties as $sortie) {
            $this->em->remove($sortie);
            $this->em->flush();
        }
        $sorties = $user->getInscriptionSortie();
        foreach ($sorties as $sortie) {
            $sortie->removeParticipant($user);
            $this->em->persist($sortie);
            $this->em->flush();
        }
        $this->em->remove($user);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_list_user');
    }
    
    #[Route('/gestionCampus', name: 'app_gestion_campus')]
    public function gestionCampus(Request $request): Response
    {
        $campus = new Campus();
        $campusForm = $this->createForm(CampusType::class, $campus);
        $campusForm->handleRequest($request);

        if($campusForm->isSubmitted() && $campusForm->isValid()) {
            $this->em->persist($campus);
            $this->em->flush();
            $this->addFlash('success', 'Vous avez bien créé le campus ' . $campus->getNom());
            return $this->redirectToRoute('app_gestion_campus');
        }

        return $this->render('admin/campusList.html.twig', [
            'campusForm' => $campusForm,
            'campusFilterForm' => $this->createForm(CampusFilterType::class),
        ]);
    }

    #[Route('/getCampus', name: 'app_get_campus')]
    public function getCampus(Request $request): Response
    {

        $campusFilterType = $this->createForm(CampusFilterType::class);
        $campusFilterType->handleRequest($request);

        if($campusFilterType->isSubmitted() && $campusFilterType->isValid()) {

            $campusList = $this->campusRepo->findAllFiltered(
                $campusFilterType->getData()?->getRecherche()
            );
    
            $campusList = array_map( function(Campus $campus){
                return [
                    "id" => $campus->getId(),
                    "nom" => $campus->getNom(),
                    "ville" => $campus->getVille()->getNom(),
                ];
            }, $campusList);

            return new JsonResponse($campusList);
        }


    }

    #[Route('/supprimerCampus/{idCampus}', name: 'app_supprimer_campus')]
    public function supprimerCampus(int $idCampus, Request $request): Response
    {
        
        $campus = $this->campusRepo->findOneById($idCampus);

        if (null === $campus) {
            return $this->redirectToRoute('app_gestion_campus');
        }

        foreach ($campus->getSorties() as $sortie) {
            $sortie->setCampus(null);
            $this->em->flush();
        }
        foreach ($campus->getEtudiants() as $etudiant) {
            $etudiant->setCampus(null);
        }
        $campus->setVille(null);
        $this->em->remove($campus);
        $this->em->flush();

        $this->addFlash('success', 'Le campus a bien été supprimé');

        return $this->redirectToRoute('app_gestion_campus');
    }

    #[Route('/gestionVilles', name: 'app_gestion_villes')]
    public function gestionVilles(Request $request): Response
    {
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()) {
            $this->em->persist($ville);
            $this->em->flush();
            $this->addFlash('success', 'Vous avez bien créé la ville ' . $ville->getNom());
            return $this->redirectToRoute('app_gestion_villes');
        }

        return $this->render('admin/villesList.html.twig', [
            'villesForm' => $villeForm,
            'villesFilterForm' => $this->createForm(VillesFilterType::class),
        ]);
    }

    #[Route('/getVilles', name: 'app_get_villes')]
    public function getVilles(Request $request): Response
    {

        $villesFilterType = $this->createForm(VillesFilterType::class);
        $villesFilterType->handleRequest($request);

        if($villesFilterType->isSubmitted() && $villesFilterType->isValid()) {

            $villesList = $this->villeRepo->findAllFiltered(
                $villesFilterType->getData()?->getRecherche()
            );
    
            $villesList = array_map( function(Ville $ville){
                return [
                    "id" => $ville->getId(),
                    "nom" => $ville->getNom(),
                    "cp" => $ville->getCp(),
                ];
            }, $villesList);

            return new JsonResponse($villesList);
        }


    }

    #[Route('/supprimerVille/{idVille}', name: 'app_supprimer_ville')]
    public function supprimerVille(int $idVille): Response
    {
        
        $ville = $this->villeRepo->findOneById($idVille);

        if (null === $ville) {
            return $this->redirectToRoute('app_gestion_campus');
        }

        foreach ($ville->getLieux() as $lieu) {
            $lieu->setVille(null);
            $this->em->flush();
        }
        foreach ($ville->getCampus() as $campus) {
            $campus->setVille(null);
            $this->em->flush();
        }

        $this->em->remove($ville);
        $this->em->flush();

        $this->addFlash('success', 'La ville a bien été supprimée');

        return $this->redirectToRoute('app_gestion_villes');
    }
}
