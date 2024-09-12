<?php

namespace App\Security\Voter;

use App\Constants\SortieConstants;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class SortieActionVoter extends Voter
{
    public const AFFICHER = 'SORTIE_AFFICHER';
    public const MODIFIER = 'SORTIE_MODIFIER';
    public const PUBLIER = 'SORTIE_PUBLIER';
    public const ANNULER = 'SORTIE_ANNULER';
    public const S_INSCRIRE = 'SORTIE_S_INSCRIRE';
    public const SE_DESINSCRIRE = 'SORTIE_SE_DESINSCRIRE';

    public function __construct(
        private readonly Security $security,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [
            self::AFFICHER,
            self::MODIFIER,
            self::PUBLIER,
            self::ANNULER,
            self::S_INSCRIRE,
            self::SE_DESINSCRIRE,
            ])
            && $subject instanceof \App\Entity\Sortie;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::AFFICHER:
                return true;
                break;
            case self::MODIFIER:
                return 
                    SortieConstants::ETAT_ANNULEE !== $subject->getEtat() 
                    && (
                    $this->security->isGranted('ROLE_ADMIN') 
                    || $user === $subject->getOrganisateur()
                    )
                ;
                break;
            case self::PUBLIER:
                return 
                    SortieConstants::ETAT_EN_CREATION === $subject->getEtat() 
                    && (
                        $this->security->isGranted('ROLE_ADMIN') 
                        || $user === $subject->getOrganisateur()
                    )
                ;
                break;
            case self::ANNULER:
                return 
                    SortieConstants::ETAT_ANNULEE !== $subject->getEtat() 
                    && (
                        $this->security->isGranted('ROLE_ADMIN') 
                        || $user === $subject->getOrganisateur()
                    )
                ;
                break;
            case self::S_INSCRIRE:
                return 
                    SortieConstants::ETAT_OUVERT === $subject->getEtat() 
                    && !$subject->getParticipants()->contains($user)
                ;
                break;
            case self::SE_DESINSCRIRE:
                return 
                    (
                        SortieConstants::ETAT_OUVERT === $subject->getEtat()
                        ||  SortieConstants::ETAT_FERME === $subject->getEtat()
                    )
                    && $subject->getParticipants()->contains($user)
                ;
                break;
        }

        return false;
    }
}
