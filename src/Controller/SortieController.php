<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Constants\SortieConstants;
use App\Model\SortieListModel;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SortieController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SortieRepository $sortieRepo,
        private readonly CampusRepository $campusRepo,
    ) {}

    #[Route('/sortie', name: 'app_sortie')]
    public function index(Request $request): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $this->sortieRepo->findAll(),
            'campus' => $this->campusRepo->findAll(),
            'selectedCampusId' => $request->query->get('campusId') ?? $this->getUser()->getCampus()->getId(),
        ]);
    }

    #[Route('/consulterSortie/{id}', name: 'app_consulter_sortie')]
    public function consulter($id): Response
    {
        return $this->render('sortie/consulterSortie.html.twig', [
            'sortie' => $this->sortieRepo->find($id)
        ]);
    }


    #[Route('/getSorties', name: 'app_get_sortie')]
    public function getSorties(Request $request): Response
    {

        $user = $this->getUser();

        $sorties = $this->sortieRepo->findAll();

        // filtre pour n'afficher les sorties non publiées de l'utilisateur connecté
        if (isset($user)) {
            $sorties = array_filter( $sorties, function(Sortie $sortie) use ($user) {
                if (($user === $sortie->getOrganisateur() && SortieConstants::ETAT_EN_CREATION === $sortie->getEtat()) || SortieConstants::ETAT_EN_CREATION !== $sortie->getEtat()) {
                    return $sortie;
                }
            });
        } else {
            $sorties = array_filter( $sorties, function(Sortie $sortie) use ($user) {
                if (SortieConstants::ETAT_EN_CREATION !== $sortie->getEtat()) {
                    return $sortie;
                }
            });
        }

        // filtre les sorties en fonction du campus
        $selectCampus = $request->request->get('select_campus');
        if (!empty($selectCampus)) {
            $sorties = array_filter($sorties, function(Sortie $sortie) use($selectCampus){
                if ($sortie->getCampus()->getId() == $selectCampus) {
                    return $sortie;
                }
            });
        }

        // filtre les sorties en fonction de la barre de recherche
        $textRecherche = $request->request->get('text_recherche');
        if (!empty($textRecherche)) {
            $sorties = array_filter($sorties, function(Sortie $sortie) use ($textRecherche){
                if (str_contains(strtolower($sortie->getNom()), strtolower($textRecherche))) {
                    return $sortie;
                }
            });
        }

        // filtre les sorties après la date de début donnée
        $dateDebut = DateTime::createFromFormat("Y-m-d", $request->request->get('date_debut'));
        if (!empty($dateDebut)) {
            $sorties = array_filter($sorties, function(Sortie $sortie) use ($dateDebut){
                if ($dateDebut < $sortie->getDateSortie()) {
                    return $sortie;
                }
            });
        }

        // filtre les sorties avant la date de fin donnée
        $dateFin = DateTime::createFromFormat("Y-m-d", $request->request->get('date_fin'));
        if (!empty($dateFin)) {
            $sorties = array_filter($sorties, function(Sortie $sortie) use ($dateFin){
                if ($dateFin > $sortie->getDateSortie()) {
                    return $sortie;
                }
            });
        }

        // filtre les sorties dont l'utilisateur connecté est organisateur
        $chkOrganisateur = $request->request->get('chk_organisateur');
        if (true == $chkOrganisateur) {
            $sorties = array_filter($sorties, function(Sortie $sortie) use ($user){
                if ($user == $sortie->getOrganisateur()) {
                    return $sortie;
                }
            });
        }

        // filtre les sorties auxquelles l'utilisateur connecté est inscrit
        $chkInscrit = $request->request->get('chk_inscrit');
        if (true == $chkInscrit) {
            $sorties = array_filter($sorties, function(Sortie $sortie) use ($user){
                if ($sortie->getParticipants()->contains($user)) {
                    return $sortie;
                }
            });
        }

        // filtre les sorties auxquelles l'utilisateur connecté n'est pas inscrit
        $chkNonInscrit = $request->request->get('chk_non_inscrit');
        if (true == $chkNonInscrit) {
            $sorties = array_filter($sorties, function(Sortie $sortie) use ($user){
                if (!$sortie->getParticipants()->contains($user)) {
                    return $sortie;
                }
            });
        }

        // filtre les sorties passées
        $chkPassees = $request->request->get('chk_passees');
        if (true == $chkPassees) {
            $sorties = array_filter($sorties, function(Sortie $sortie) use ($user){
                if (SortieConstants::ETAT_PASSE === $sortie->getEtat()) {
                    return $sortie;
                }
            });
        }

        $sortiesList = array_map( function(Sortie $sortie) use($user){
            return [
                "id" => $sortie->getId(),
                "nom" => $sortie->getNom(),
                "dateSortie" => $sortie->getDateSortie()->format('d-m-Y H:i:s'),
                "dateFinInscritpion" => $sortie->getDateFinInscription()->format('d-m-Y H:i:s'),
                "inscritsPlaces" => $sortie->getNombreParticipants() . '/' . $sortie->getNombrePlace(),
                "organisateur" => $sortie->getOrganisateur()->getPrenom() . ' ' . $sortie->getOrganisateur()->getNom(),
                "organisateurId" => $sortie->getOrganisateur()->getId(),
                "peutModifier" => $this->isGranted('ROLE_ADMIN') || $user === $sortie->getOrganisateur(),
                "etat" => $sortie->getEtat(),
                "inscrit" => $sortie->getParticipants()->contains($user),
            ];
        }, $sorties);

        return new JsonResponse($sortiesList);
    }

    #[Route('/sortie/desinscription/{idSortie}', name: 'app_desinscription')]
    public function desinscription($idSortie): Response
    {
        $user = $this->getUser();
        $sortie = $this->sortieRepo->findOneById($idSortie);
        if ($sortie->getParticipants()->contains($user)) {
            $sortie->removeParticipant($user);
            $this->em->flush();
            $this->addFlash('success', 'Vous avez bien été désinscrit de ' . $sortie->getNom());
        }
        return $this->redirectToRoute('app_sortie', ['campusId' => $sortie->getCampus()->getId()]);
    }

    #[Route('/sortie/inscription/{idSortie}', name: 'app_inscription')]
    public function inscription($idSortie): Response
    {
        $user = $this->getUser();
        $sortie = $this->sortieRepo->findOneById($idSortie);
        if (!$sortie->getParticipants()->contains($user) ) {
            if ($sortie->getNombreParticipants() < $sortie->getNombrePlace()) {
                $sortie->addParticipant($user);
                $this->em->flush();
                $this->addFlash('success', 'Vous avez bien été inscrit à ' . $sortie->getNom());
            } else {
                $this->addFlash('error', 'Vous n\'avez pas pu vous inscrire à ' . $sortie->getNom() . ': aucune place restante');
            }
        } else {
            $this->addFlash('success', 'Vous êtes déjà inscrit à ' . $sortie->getNom());
        }
        return $this->redirectToRoute('app_sortie', ['campusId' => $sortie->getCampus()->getId()]);
    }

    #[Route('/sortie/publier/{idSortie}', name: 'app_publier')]
    public function publier($idSortie): Response
    {
        $user = $this->getUser();
        $sortie = $this->sortieRepo->findOneById($idSortie);
        if ($user === $sortie->getOrganisateur()) {
            $sortie->setPubliee(true);
            $this->em->flush();
            $this->addFlash('success', 'Vous avez bien publié la sortie ' . $sortie->getNom());
        }
        return $this->redirectToRoute('app_sortie', ['campusId' => $sortie->getCampus()->getId()]);
    }

    #[Route('/sortie/create', name: 'app_creation_sortie')]
    public function create(Request $request): Response
    {
        $user = $this->getUser();

        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setOrganisateur($user);
            $sortie->setCampus(campus: $user->getCampus());

            if ($sortieForm->get('enregistrer')->isClicked()) {
                $sortie->setPubliee(false);
            } elseif ($sortieForm->get('publier')->isClicked()) {
                $sortie->setPubliee(true);
            }

            $this->em->persist($sortie);
            $this->em->flush();

            return $this->redirectToRoute('app_sortie');
        }

        return $this->render('sortie/create.html.twig', [
            'user' => $user,
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    #[Route('/get-lieu', name: 'app_get_lieu', methods: ['GET'])]
    public function getLieu(Request $request, LieuRepository $lieuRepository): JsonResponse
    {
        $lieuId = $request->query->get('lieuId');

        $lieu = $lieuRepository->find($lieuId);

        if (!$lieu) {
            return new JsonResponse(['error' => 'Lieu non trouvée'], 404);
        }

        return new JsonResponse(['rue' => $lieu->getRue(), 'latitude' => $lieu->getLatitude(), 'longitude' => $lieu->getLongitude(), 'codePostal' => $lieu->getVille()->getCp()]);
    }
}
