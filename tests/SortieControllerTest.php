<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class SortieControllerTest extends WebTestCase
{
    public function testRedirectionIfNotAuthenticated()
    {
        //Création d'un navigateur simulé
        $client = static::createClient();

        //Requête GET vers la page d'affichage d'une sortie
        $client->request('GET', '/consulterSortie/1');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $this->assertResponseRedirects('/login');
    }

    public function testAffichageSortie()
    {

        //Création d'un navigateur simulé
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->find(1);

        //On simule l'authentification de cet utilisateur
        $client->loginUser($testUser);

        //Requête GET vers la page d'affichage d'une sortie
        $client->request('GET', '/consulterSortie/1');

        //On vérifie que la page est bien chargée
        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('#nomSortie', 'Sortie Jardin de la Brèche');
    }

    public function testFormAjoutSortie()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->find(1);

        //On simule l'authentification de cet utilisateur
        $client->loginUser($testUser);

        // Aller à la page du formulaire d'ajout de sortie
        $client->request('GET', '/sortie/create');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Enregistrer', [
            'sortie[nom]' => 'Mangeage de pizza',
            'sortie[dateSortie]' => '2024-09-15 12:30:00',
            'sortie[dateFinInscription]' => '2024-09-14 22:30:00',
            'sortie[nombrePlace]' => '5',
            'sortie[duree]' => '90',
            'sortie[description]' => 'On mange des pizzas au parc quoi',
            'sortie[campus]' => '2',
            'sortie[ville]' => '2',
            'sortie[lieu]' => '3',
        ]);
    }

    public function testFormAjoutSortieFailed()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->find(1);

        //On simule l'authentification de cet utilisateur
        $client->loginUser($testUser);

        // Aller à la page du formulaire d'ajout de sortie
        $client->request('GET', '/sortie/create');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Enregistrer', [
            'sortie[dateSortie]' => '2024-09-15 12:30:00',
            'sortie[dateFinInscription]' => '2024-09-14 22:30:00',
            'sortie[nombrePlace]' => '5',
            'sortie[duree]' => '90',
            'sortie[description]' => 'On mange des pizzas au parc quoi',
            'sortie[campus]' => '2',
            'sortie[ville]' => '2',
            'sortie[lieu]' => '3',
        ]);

        $this->assertSelectorTextContains('.invalid-feedback', 'Le nom doit être renseigné');
    }
}
