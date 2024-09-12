<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Form\SortieType;
use App\Repository\VilleRepository;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Form\AnnulationSortieFormType;
use App\Form\AnnulationSortieType;
use App\Form\SortieFilterType;
use App\Model\SortieActionModel;
use App\Model\SortieListItemModel;
use App\Security\Voter\SortieActionVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SortieController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SortieRepository $sortieRepo,
        private readonly CampusRepository $campusRepo,
        private readonly LieuRepository $lieuRepository,
        private readonly VilleRepository $villeRepository,
    ) {}

    #[Route('/sortie', name: 'app_sortie')]
    #[Route('/', name: 'app_sortie_blank')]
    public function index(Request $request): Response
    {
        

        return $this->render('sortie/index.html.twig', [
            'sorties' => $this->sortieRepo->findAll(),
            'campus' => $this->campusRepo->findAll(),
            'selectedCampusId' => $request->query->get('campusId') ?? $this->getUser()->getCampus()?->getId(),
            'formRecherche' => $this->createForm(
                SortieFilterType::class, 
                null, 
                [
                    'campus' => null !== $request->query->get('campusId') 
                    ? $this->campusRepo->find($request->query->get('campusId')) 
                    : (
                        (null !== $this->getUser()->getCampus()) 
                        ? $this->getUser()->getCampus() 
                        : null
                    ),
                ]
            ),
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
    public function getSorties(Request $request, SerializerInterface $serializer): Response
    {

        $user = $this->getUser();

        $sortieFilterType = $this->createForm(SortieFilterType::class);
        $sortieFilterType->handleRequest($request);

        if($sortieFilterType->isSubmitted() && $sortieFilterType->isValid()) {

            $sorties = $this->sortieRepo->findAllFiltered(
                $this->getUser(),
                $this->isGranted('ROLE_ADMIN'),
                $sortieFilterType->getData()->getCampus(),
                $sortieFilterType->getData()->getRecherche(),
                $sortieFilterType->getData()->getDateDebut(),
                $sortieFilterType->getData()->getDateFin(),
                $sortieFilterType->getData()->isOrganisateur(),
                $sortieFilterType->getData()->isInscrit(),
                $sortieFilterType->getData()->isNonInscrit(),
                $sortieFilterType->getData()->isPassee(),
            );

            $sortiesList = array_map( function(Sortie $sortie) use($user, $serializer){
                $sortieListItem = SortieListItemModel::getSortieListItem($sortie, $user);
                if ($this->isGranted(SortieActionVoter::AFFICHER, $sortie)) {
                    $sortieListItem->addAction(SortieActionModel::getSortieActionItem('Afficher', $this->generateUrl('app_consulter_sortie', ['id' => $sortie->getId(),])));
                }
                if ($this->isGranted(SortieActionVoter::MODIFIER, $sortie)) {
                    $sortieListItem->addAction(SortieActionModel::getSortieActionItem('Modifier', $this->generateUrl('app_update_sortie', ['idSortie' => $sortie->getId(),])));
                }
                if ($this->isGranted(SortieActionVoter::PUBLIER, $sortie)) {
                    $sortieListItem->addAction(SortieActionModel::getSortieActionItem('Publier', $this->generateUrl('app_publier', ['idSortie' => $sortie->getId()])));
                }
                if ($this->isGranted(SortieActionVoter::ANNULER, $sortie)) {
                    $sortieListItem->addAction(SortieActionModel::getSortieActionItem('Annuler', $this->generateUrl('app_annuler', ['idSortie' => $sortie->getId()])));
                }
                if ($this->isGranted(SortieActionVoter::S_INSCRIRE, $sortie)) {
                    $sortieListItem->addAction(SortieActionModel::getSortieActionItem('S\'inscrire', $this->generateUrl('app_inscription', ['idSortie' => $sortie->getId()])));
                }
                if ($this->isGranted(SortieActionVoter::SE_DESINSCRIRE, $sortie)) {
                    $sortieListItem->addAction(SortieActionModel::getSortieActionItem('Se désister', $this->generateUrl('app_desinscription', ['idSortie' => $sortie->getId()])));
                }
                return $sortieListItem;
            }, $sorties);

            return new Response($serializer->serialize($sortiesList, 'json'));
        }
        return new JsonResponse('erreur');
    }

    #[Route('/sortie/desinscription/{idSortie}', name: 'app_desinscription')]
    public function desinscription($idSortie): Response
    {
        $user = $this->getUser();
        $sortie = $this->sortieRepo->findOneById($idSortie);

        if ($this->isGranted(SortieActionVoter::SE_DESINSCRIRE, $sortie)) {
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
        if ($this->isGranted(SortieActionVoter::S_INSCRIRE, $sortie)) {
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
        if ($this->isGranted(SortieActionVoter::PUBLIER, $sortie)) {
            $sortie->setPubliee(true);
            $this->em->flush();
            $this->addFlash('success', 'Vous avez bien publié la sortie ' . $sortie->getNom());
        }
        return $this->redirectToRoute('app_sortie', ['campusId' => $sortie->getCampus()->getId()]);
    }

    #[Route('/annulerSortie/{idSortie}', name: 'app_annuler')]
    public function annuler($idSortie, Request $request): Response
    {
        $sortie = $this->sortieRepo->findOneById($idSortie);
        if (!$this->isGranted(SortieActionVoter::ANNULER, $sortie))
        {
            return $this->redirectToRoute('app_sortie');
        }

        $annulationForm = $this->createForm(AnnulationSortieType::class);
        $annulationForm->handleRequest($request);

        if ($annulationForm->isSubmitted() && $annulationForm->isValid()) {
            $sortie->setDescription($sortie->getDescription() . ' - ' . $annulationForm->getData()['motif']);
            $sortie->setAnnulation(true);
            $this->em->flush();
            return $this->redirectToRoute('app_sortie');
        }
        $this->addFlash('success', 'Vous avez bien annulé la sortie ' . $sortie->getNom());
        return $this->render('sortie/annulerSortie.html.twig', [
            'annulationForm' => $annulationForm,
        ]);
    }

    #[Route('/sortie/create', name: 'app_creation_sortie')]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        $campus = $user->getCampus();

        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'default_campus' => $campus
        ]);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setOrganisateur($user);

            if ($sortieForm->get('enregistrer')->isClicked()) {
                $sortie->setPubliee(false);
                $this->addFlash('success', 'La sortie ' . $sortie->getNom() . '  a bien été créée !');
            } elseif ($sortieForm->get('publier')->isClicked()) {
                $sortie->setPubliee(true);
                $this->addFlash('success', 'La sortie ' . $sortie->getNom() . ' a bien été créée et publiée avec succès!');
            } else {
                $this->addFlash('error', "La sortie " . $sortie->getNom() . "  n'a été créée, il y a eu un soucis !");
            }

            $this->em->persist($sortie);
            $this->em->flush();


            return $this->redirectToRoute('app_sortie', ['campusId' => $sortie->getCampus()->getId()]);
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

    #[Route('/sortie/update/{idSortie}', name: 'app_update_sortie')]
    public function update($idSortie, Request $request): Response
    {
        $sortie = $this->sortieRepo->find($idSortie);

        $sortieForm = $this->createForm(SortieType::class, $sortie, ['is_edit' => true]);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            if ($sortieForm->get('modifier')->isClicked()) {
                $this->addFlash('success', 'La sortie ' . $sortie->getNom() . ' a bien été modifiée !');
            } else {
                $this->addFlash('error', "La sortie " . $sortie->getNom() . " n'a été modifiée, il y a eu un soucis !");
            }

            $this->em->persist($sortie);
            $this->em->flush();


            return $this->redirectToRoute('app_consulter_sortie', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/updateSortie.html.twig', [
            'sortie' => $sortie,
            'sortieForm' => $sortieForm->createView(),
        ]);
    }
    
    #[Route('/addLieu', name: 'app_add_lieu')]
public function addLieu(Request $request, LieuRepository $lieuRepository, VilleRepository $villeRepository, EntityManagerInterface $entityManager): JsonResponse
{
    // Récupération des données de la requête POST
    $ville = $request->request->get('ville');
    $nom = $request->request->get('nom');
    $longitude = $request->request->get('longitude');
    $latitude = $request->request->get('latitude');
    $rue = $request->request->get('rue');


   
    // Vérifier si le lieu existe déjà dans la base de données
    $lieux = $villeRepository->findOneBy(['nom' => $ville])->getLieux();
    $ville = $villeRepository->findOneBy(['nom' => $ville]);
    $lieuExist = false;
    foreach ($lieux as $lieu) {
        if ($lieu->getNom() === $nom) {
            $lieuExist = true;
            break;
        }
    }
    
     if ($lieuExist) {
         // Si le lieu existe, renvoyer un message d'erreur
         
         return new JsonResponse(['message' => "echec"], 400);  // Code 400 : Bad Request
     } else {
         // Si le lieu n'existe pas, créer un nouveau lieu
         $lieu = new Lieu();
         $lieu->setNom($nom);
         $lieu->setRue($rue);
         $lieu->setLongitude($longitude);
         $lieu->setLatitude($latitude);
         $lieu->setVille($ville);
         // Sauvegarder le lieu dans la base de données
         $entityManager->persist($lieu);
         $entityManager->flush();
         // Retourner une réponse JSON avec un message de succès
         return new JsonResponse(['message' => "success"], 201);  // Code 201 : Created
     }
    }


    #[Route('/getLieuFromVille/{id}', name: 'app_get_lieu_from_ville', methods: ['GET'])]
    public function getLieuFromVille($id,Request $request): JsonResponse
    {
        $tabLieu=[];
        $ville=$this->villeRepository->find($id)->getLieux();
        foreach ($ville as $value){
            $tabLieu[]=[$value->getId(),$value->getNom()];
        }
        return new JsonResponse($tabLieu);
    }
}
