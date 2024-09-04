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
        $manager->persist($user1);

        $user2 = new User();
        $user2->setPassword('$2y$13$slruzjo7Bv.8QG4kkntzyOl08OgWmdH53jTx0RG1zxqJNuTu7ci.u'); // mdp : password
        $user2->setEmail('test2@test.fr');
        $user2->setNom('Kebab2');
        $user2->setPrenom('Gilles2');
        $user2->setTelephone('33450453289');
        $user2->setPseudo('Pseudo2');
        $user2->setRoles(['ROLE_USER','ROLE_ADMIN']);
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
        $manager->persist($user3);


        $sortieParc2 = new Sortie();
        $sortieParc2->setNom('sortie Parc');
        $sortieParc2->setCampus($campusNiort);
        $sortieParc2->setLieu($lieuParc);
        $sortieParc2->setDateSortie(new DateTime('+10 days'));
        $sortieParc2->setDateFinInscription(new DateTime('+5 days'));
        $sortieParc2->setNombrePlace(10);
        $sortieParc2->setDuree(90);
        $sortieParc2->setDescription('go parc');
        $sortieParc2->setAnnulation(false);
        $sortieParc2->setPubliee(false);
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
        $sortieMediatheque->setOrganisateur($user1);
        $sortieMediatheque->addParticipant($user1);
        $sortieMediatheque->addParticipant($user2);
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
        $sortiePiscine->setPubliee(true);
        $sortiePiscine->setOrganisateur($user2);
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
        $sortiePiscine2->setOrganisateur($user2);
        $manager->persist($sortiePiscine2);

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
        $sortieParc->setPubliee(true);
        $sortieParc->setOrganisateur($user1);
        $manager->persist($sortieParc);


        $manager->flush();
    }
}
