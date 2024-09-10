<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $photoProfilTest = 'data:image/jpeg;base64,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';


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

        $saintAmandMontrond = new Ville();
        $saintAmandMontrond->setNom('Saint-Amand-Montrond');
        $saintAmandMontrond->setCp('18210');
        $manager->persist($saintAmandMontrond);



        $campusCDB = new Campus();
        $campusCDB->setNom('Campus Rennes');
        $campusCDB->setVille($chartresDeBretagne);
        $manager->persist($campusCDB);

        $campusNiort = new Campus();
        $campusNiort->setNom('Campus Niort');
        $campusNiort->setVille($niort);
        $manager->persist($campusNiort);

        $campusNantes = new Campus();
        $campusNantes->setNom('Campus Nantes');
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



        $userJosette = new User();
        $userJosette->setPseudo('Josette');
        $userJosette->setPrenom('Josette');
        $userJosette->setNom('Chalala');
        $userJosette->setEmail('josette@gmail.com');
        $userJosette->setTelephone('0123456789');
        $userJosette->setPassword('$2y$10$llCLAI1SeEwdQ/kOhY4uwu4mMPsM7XGTCFJ8KZ7jID.LI1Lo.vUh6');
        $userJosette->setRoles(['ROLE_ADMIN']);
        $userJosette->setCampus($campusNiort);
        $userJosette->setPhoto($photoProfilTest);
        $manager->persist($userJosette);

        $userGeorges = new User();
        $userGeorges->setPseudo('Georgio');
        $userGeorges->setPrenom('Georges');
        $userGeorges->setNom('Poulala');
        $userGeorges->setEmail('georges@monmail.com');
        $userGeorges->setTelephone('0567891234');
        $userGeorges->setPassword('$2y$10$mAla0la5xd8Yq4JQR5qArOV2sXlzaESyR7XNNKilkya59b6E6pVe2');
        $userGeorges->setRoles(['ROLE_USER']);
        $userGeorges->setCampus($campusCDB);
        $userGeorges->setPhoto($photoProfilTest);
        $manager->persist($userGeorges);

        $userFrenegonde = new User();
        $userFrenegonde->setPseudo('Frefre');
        $userFrenegonde->setPrenom('Frénégonde');
        $userFrenegonde->setNom('Tuelili');
        $userFrenegonde->setEmail('frefre44@tonmail.com');
        $userFrenegonde->setTelephone('0654782114');
        $userFrenegonde->setPassword('$2y$10$CPMm2Hq.K.2OYWKBWktoguT7lpKHgil6tok4hHQ9roxr5VOp6jnrG');
        $userFrenegonde->setRoles(['ROLE_USER']);
        $userFrenegonde->setCampus($campusNantes);
        $userFrenegonde->setPhoto($photoProfilTest);
        $manager->persist($userFrenegonde);



        $user1 = new User();
        $user1->setPassword('$2y$13$slruzjo7Bv.8QG4kkntzyOl08OgWmdH53jTx0RG1zxqJNuTu7ci.u'); // mdp : password
        $user1->setEmail('test1@test.fr');
        $user1->setNom('Kebab');
        $user1->setPrenom('Gilles');
        $user1->setTelephone('33450453289');
        $user1->setPseudo('Pseudo1');
        $user1->setRoles(['ROLE_USER']);
        $user1->setCampus($campusNiort);
        $user1->setPhoto($photoProfilTest);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setPassword('$2y$13$slruzjo7Bv.8QG4kkntzyOl08OgWmdH53jTx0RG1zxqJNuTu7ci.u'); // mdp : password
        $user2->setEmail('test2@test.fr');
        $user2->setNom('Kebab2');
        $user2->setPrenom('Gilles2');
        $user2->setTelephone('33450453289');
        $user2->setPseudo('Pseudo2');
        $user2->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $user2->setPhoto($photoProfilTest);
        $user2->setCampus($campusCDB);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setPassword('$2y$13$slruzjo7Bv.8QG4kkntzyOl08OgWmdH53jTx0RG1zxqJNuTu7ci.u'); // mdp : password
        $user3->setEmail('test3@test.fr');
        $user3->setNom('Kebab2');
        $user3->setPrenom('Gilles3');
        $user3->setTelephone('33450453289');
        $user3->setPseudo('Pseudo3');
        $user3->setRoles(['ROLE_USER']);
        $user3->setCampus($campusNantes);
        $user3->setPhoto($photoProfilTest);
        $manager->persist($user3);


        $sortieParc2 = new Sortie();
        $sortieParc2->setNom('Sortie Jardin de la Brèche');
        $sortieParc2->setCampus($campusNiort);
        $sortieParc2->setLieu($lieuParc);
        $sortieParc2->setDateSortie(new DateTime('+10 days'));
        $sortieParc2->setDateFinInscription(new DateTime('+5 days'));
        $sortieParc2->setNombrePlace(10);
        $sortieParc2->setDuree(90);
        $sortieParc2->setDescription('go to le parc');
        $sortieParc2->setAnnulation(false);
        $sortieParc2->setPubliee(false);
        $sortieParc2->addParticipant($user3);
        $sortieParc2->setOrganisateur($user1);
        $manager->persist($sortieParc2);


        
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
        $sortieMediatheque->setPubliee(true);
        $sortieMediatheque->setOrganisateur($user3);
        $sortieMediatheque->addParticipant($user3);
        $manager->persist($sortieMediatheque);

        $sortiePiscine = new Sortie();
        $sortiePiscine->setNom('Sortie Piscine La Conterie');
        $sortiePiscine->setCampus($campusCDB);
        $sortiePiscine->setLieu($lieuPiscine);
        $sortiePiscine->setDateSortie(new DateTime('+10 days'));
        $sortiePiscine->setDateFinInscription(new DateTime('+5 days'));
        $sortiePiscine->setNombrePlace(10);
        $sortiePiscine->setDuree(90);
        $sortiePiscine->setDescription('go nager');
        $sortiePiscine->setAnnulation(false);
        $sortiePiscine->setPubliee(true);
        $sortiePiscine->addParticipant($user3);
        $sortiePiscine->setOrganisateur($user3);
        $manager->persist($sortiePiscine);

        $sortiePiscine2 = new Sortie();
        $sortiePiscine2->setNom('sortie Piscine plein');
        $sortiePiscine2->setCampus($campusCDB);
        $sortiePiscine2->setLieu($lieuPiscine);
        $sortiePiscine2->setDateSortie(new DateTime('+10 days'));
        $sortiePiscine2->setDateFinInscription(new DateTime('-1 days'));
        $sortiePiscine2->setNombrePlace(1);
        $sortiePiscine2->setDuree(90);
        $sortiePiscine2->setDescription('go nager');
        $sortiePiscine2->setAnnulation(false);
        $sortiePiscine2->setPubliee(true);
        $sortiePiscine2->addParticipant($user2);
        $sortiePiscine2->addParticipant($user1);
        $sortiePiscine2->setOrganisateur($user2);
        $manager->persist($sortiePiscine2);

        $sortieParc = new Sortie();
        $sortieParc->setNom('Sortie Parc des Brizeaux');
        $sortieParc->setCampus($campusNiort);
        $sortieParc->setLieu($lieuParc);
        $sortieParc->setDateSortie(new DateTime('+10 days'));
        $sortieParc->setDateFinInscription(new DateTime('+5 days'));
        $sortieParc->setNombrePlace(10);
        $sortieParc->setDuree(90);
        $sortieParc->setDescription('go parc');
        $sortieParc->setAnnulation(false);
        $sortieParc->setPubliee(true);
        $sortieParc->addParticipant($user3);
        $sortieParc->setOrganisateur($user1);
        $manager->persist($sortieParc);

        $sortieParc2 = new Sortie();
        $sortieParc2->setNom('Sortie Sport');
        $sortieParc2->setCampus($campusNantes);
        $sortieParc2->setLieu($lieuGymnase);
        $sortieParc2->setDateSortie(new DateTime('+10 days'));
        $sortieParc2->setDateFinInscription(new DateTime('+5 days'));
        $sortieParc2->setNombrePlace(10);
        $sortieParc2->setDuree(90);
        $sortieParc2->setDescription('go faire du cardio');
        $sortieParc2->setAnnulation(false);
        $sortieParc2->setPubliee(false);
        $sortieParc2->setOrganisateur($user2);
        $manager->persist($sortieParc2);

        for ($i = 0; $i < 200; $i++) {
            $sortieParc3 = new Sortie();
            $sortieParc3->setNom('sortie Parc');
            $sortieParc3->setCampus($campusNiort);
            $sortieParc3->setLieu($lieuParc);
            $sortieParc3->setDateSortie(new DateTime('+10 days'));
            $sortieParc3->setDateFinInscription(new DateTime('+5 days'));
            $sortieParc3->setNombrePlace(10);
            $sortieParc3->setDuree(90);
            $sortieParc3->setDescription('go parc');
            $sortieParc3->setAnnulation(true);
            $sortieParc3->setPubliee(false);
            $sortieParc3->setOrganisateur($user1);
            $manager->persist($sortieParc3);
        }

        $manager->flush();
    }
}
