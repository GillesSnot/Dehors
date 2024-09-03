<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setPassword('$2y$13$slruzjo7Bv.8QG4kkntzyOl08OgWmdH53jTx0RG1zxqJNuTu7ci.u'); // mdp : password
        $user1->setEmail('test@test.fr');
        $user1->setNom('De la tourrete');
        $user1->setPrenom('Gilles');
        $user1->setTelephone('33450453289');
        $user1->setPseudo('Pseudo');
        $manager->persist($user1);



        $chartresDeBretagne = new Ville();
        $chartresDeBretagne->setNom('Chartres-de-Bretagne');
        $chartresDeBretagne->setCp('35131');
        $manager->persist($chartresDeBretagne);

        $niort = new Ville();
        $niort->setNom('Niort');
        $niort->setCp('79000');
        $manager->persist($niort);

        $nantes = new Ville();
        $nantes->setNom('Nantes');
        $nantes->setCp('44000');
        $manager->persist($nantes);



        $campusCDB = new Campus();
        $campusCDB->setVille($chartresDeBretagne);
        $manager->persist($campusCDB);

        $campusNiort = new Campus();
        $campusNiort->setVille($niort);
        $manager->persist($campusNiort);

        $campusNantes = new Campus();
        $campusNantes->setVille($nantes);
        $manager->persist($campusNantes);



        $lieuMediatheque = new Lieu();
        $lieuMediatheque->setNom('Mediathèque');
        $lieuMediatheque->setRue('2 Avenue de la Chaussairie');
        $lieuMediatheque->setLatitude(1);
        $lieuMediatheque->setLongitude(2);
        $lieuMediatheque->setVille($chartresDeBretagne);
        $manager->persist($lieuMediatheque);

        $lieuPiscine = new Lieu();
        $lieuPiscine->setNom('Piscine');
        $lieuPiscine->setRue('5 Avenue de la Chaussairie');
        $lieuPiscine->setLatitude(1);
        $lieuPiscine->setLongitude(8);
        $lieuPiscine->setVille($chartresDeBretagne);
        $manager->persist($lieuPiscine);

        $lieuParc = new Lieu();
        $lieuParc->setNom('Parc');
        $lieuParc->setRue('5 Avenue de Niort');
        $lieuParc->setLatitude(1);
        $lieuParc->setLongitude(8);
        $lieuParc->setVille($niort);
        $manager->persist($lieuParc);

        $lieuGymnase = new Lieu();
        $lieuGymnase->setNom('Gymnase');
        $lieuGymnase->setRue('5 Avenue de Nantes');
        $lieuGymnase->setLatitude(1);
        $lieuGymnase->setLongitude(8);
        $lieuGymnase->setVille($nantes);
        $manager->persist($lieuGymnase);



        $sortieMediatheque = new Sortie();
        $sortieMediatheque->setNom('sortie Mediathèque');
        $sortieMediatheque->setCampus($campusCDB);
        $sortieMediatheque->setLieu($lieuMediatheque);
        $sortieMediatheque->setDateSortie(new DateTime('+10 days'));
        $sortieMediatheque->setDateFinInscription(new DateTime('+5 days'));
        $sortieMediatheque->setNombrePlace(10);
        $sortieMediatheque->setDuree(90);
        $sortieMediatheque->setDescription('go lire des livres');
        $sortieMediatheque->setAnnulation(false);
        $sortieMediatheque->setOrganisateur($user1);
        $manager->persist($sortieMediatheque);

        $sortiePiscine = new Sortie();
        $sortiePiscine->setNom('sortie Piscine');
        $sortiePiscine->setCampus($campusCDB);
        $sortiePiscine->setLieu($lieuPiscine);
        $sortiePiscine->setDateSortie(new DateTime('+10 days'));
        $sortiePiscine->setDateFinInscription(new DateTime('+5 days'));
        $sortiePiscine->setNombrePlace(10);
        $sortiePiscine->setDuree(90);
        $sortiePiscine->setDescription('go nager');
        $sortiePiscine->setAnnulation(false);
        $manager->persist($sortiePiscine);

        $sortieParc = new Sortie();
        $sortieParc->setNom('sortie Parc');
        $sortieParc->setCampus($campusNiort);
        $sortieParc->setLieu($lieuParc);
        $sortieParc->setDateSortie(new DateTime('+10 days'));
        $sortieParc->setDateFinInscription(new DateTime('+5 days'));
        $sortieParc->setNombrePlace(10);
        $sortieParc->setDuree(90);
        $sortieParc->setDescription('go parc');
        $sortieParc->setAnnulation(false);
        $manager->persist($sortieParc);

        $manager->flush();
    }
}
