<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PSEUDO', fields: ['pseudo'])]
#[UniqueEntity(fields: ['pseudo'], message: 'There is already an account with this pseudo')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $email;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 255)]
    private string $pseudo;

    #[ORM\Column(length: 255)]
    private string $prenom;

    #[ORM\Column(length: 255)]
    private string $nom;

    #[ORM\Column(length: 255)]
    private string $telephone;

    #[ORM\Column(type: Types::BLOB, nullable:true)]
    private $photo;

    #[ORM\ManyToOne(inversedBy: 'etudiants')]
    private Campus $campus;

    /**
     * @var Collection<int, sortie>
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participant')]
    private Collection $inscriptionSortie;

    /**
     * @var Collection<int, sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'organisateur')]
    private Collection $sortiesOrganisees;

    #[ORM\OneToOne(mappedBy: 'User', cascade: ['persist', 'remove'])]
    private ?Token $token = null;

    

    public function __construct()
    {
        $this->inscriptionSortie = new ArrayCollection();
        $this->sortiesOrganisees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $userRoles = $this->roles;
        // guarantee every user at least has ROLE_USER
        foreach( $userRoles as $userRole){
            $roles[]= $userRole;
        }
       
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, sortie>
     */
    public function getInscriptionSortie(): Collection
    {
        return $this->inscriptionSortie;
    }

    public function addInscriptionSortie(sortie $inscriptionSortie): static
    {
        if (!$this->inscriptionSortie->contains($inscriptionSortie)) {
            $this->inscriptionSortie->add($inscriptionSortie);
        }

        return $this;
    }

    public function removeInscriptionSortie(sortie $inscriptionSortie): static
    {
        $this->inscriptionSortie->removeElement($inscriptionSortie);

        return $this;
    }

    /**
     * @return Collection<int, sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(sortie $sortiesOrganisee): static
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees->add($sortiesOrganisee);
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(sortie $sortiesOrganisee): static
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getToken(): ?Token
    {
        return $this->token;
    }

    public function setToken(?Token $token): static
    {
        // unset the owning side of the relation if necessary
        if ($token === null && $this->token !== null) {
            $this->token->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($token !== null && $token->getUser() !== $this) {
            $token->setUser($this);
        }

        $this->token = $token;

        return $this;
    }

   
}
