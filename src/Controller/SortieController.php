<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use App\Constants\SortieConstants;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\AnnulationSortieFormType;
use App\Form\AnnulationSortieType;
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
    #[Route('/', name: 'app_sortie_blank')]
    public function index(Request $request): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $this->sortieRepo->findAll(),
            'campus' => $this->campusRepo->findAll(),
            'selectedCampusId' => $request->query->get('campusId') ?? $this->getUser()->getCampus()?->getId(),
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

        // filtre les sorties de plus d'un mois archivées
        $sorties = array_filter($sorties, function(Sortie $sortie){
            if (SortieConstants::ETAT_ARCHIVEE !== $sortie->getEtat()) {
                return $sortie;
            }
        });

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
                "dateSortie" => $sortie->getDateSortie()->format('d-m-Y H:i'),
                "dateFinInscritpion" => $sortie->getDateFinInscription()->format('d-m-Y'),
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

    #[Route('/annulerSortie/{idSortie}', name: 'app_annuler')]
    public function annuler($idSortie, Request $request): Response
    {
        $user = $this->getUser();
        $sortie = $this->sortieRepo->findOneById($idSortie);
        if (
            $user !== $sortie->getOrganisateur() 
            || SortieConstants::ETAT_ANNULEE === $sortie->getEtat()
            || new DateTime('now') > $sortie->getDateSortie() 
            ) 
        {
            return $this->redirectToRoute('app_sortie');
        }

        $annulationForm = $this->createForm(AnnulationSortieType::class);
        $annulationForm->handleRequest($request);

        if($annulationForm->isSubmitted() && $annulationForm->isValid()) {
            $sortie->setDescription($sortie->getDescription() . ' - ' . $annulationForm->getData()['motif']);
            $sortie->setAnnulation(true);
            $this->em->flush();
        }
        $this->addFlash('success', 'Vous avez bien annulé la sortie ' . $sortie->getNom());
        return $this->render('sortie/annulerSortie.html.twig', [
            'annulationForm' => $annulationForm,
        ]);
    }
}
    