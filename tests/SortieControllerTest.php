<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class SortieControllerTest extends WebTestCase
{
    public function testRedirectionIfNotAuthenticated() {
        //Création d'un navigateur simulé
        $client = static::createClient();

        //Requête GET vers la page d'affichage d'une sortie
        $client->request('GET', '/consulterSortie/1');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $this->assertResponseRedirects('/login');
    }
    public function testAffichageSortie() {

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

}
