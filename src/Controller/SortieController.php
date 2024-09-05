<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SortieController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SortieRepository $sortieRepo,
    ) {}

    #[Route('/sortie', name: 'app_sortie')]
    public function index(): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $this->sortieRepo->findAll()
        ]);
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
