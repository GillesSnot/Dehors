<?php

namespace App\Entity;

use App\Constants\SortieConstants;
use App\Repository\SortieRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le nom doit être renseigné")]
    private string $nom;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date de la sortie doit être renseignée")]
    #[Assert\GreaterThan('today UTC', message: "La date de sortie doit être postérieure à la date d'aujourdhui")]
    private ?DateTimeInterface $dateSortie;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date de la fin d'inscription doit être renseignée")]
    #[Assert\LessThan(propertyPath: "dateSortie", message: "La limite de date d'inscription ne peut pas être postérieure à la date de sortie, voyez vous ?")]
    private ?DateTimeInterface $dateFinInscription;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le nombre de place doit être renseigné")]
    #[Assert\Range(
        min: 2,
        max: 100,
        notInRangeMessage: "Le nombre de places doit être compris entre 2 et 100"
    )]
    private int $nombrePlace;

    #[ORM\Column]
    #[Assert\NotNull(message: "La durée doit être renseignée")]
    #[Assert\Range(
        min: 1,
        minMessage: "La durée ne peut être inférieur à une minute",
    )]
    private int $duree;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(options: ["default" => false])]
    private bool $annulation = false;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private Lieu $lieu;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?Campus $campus;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'inscriptionSortie')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'sortiesOrganisees')]
    private User $organisateur;

    #[ORM\Column(options: ["default" => false])]
    private bool $publiee = false;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateSortie(): ?DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(?\DateTimeInterface $dateSortie): static
    {
        $this->dateSortie = $dateSortie;

        return $this;
    }

    public function getDateFinInscription(): ?\DateTimeInterface
    {
        return $this->dateFinInscription;
    }

    public function setDateFinInscription(?DateTimeInterface $dateFinInscription): static
    {
        $this->dateFinInscription = $dateFinInscription;

        return $this;
    }

    public function getNombrePlace(): ?int
    {
        return $this->nombrePlace;
    }

    public function setNombrePlace(int $nombrePlace): static
    {
        $this->nombrePlace = $nombrePlace;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isAnnulation(): ?bool
    {
        return $this->annulation;
    }

    public function setAnnulation(bool $annulation): static
    {
        $this->annulation = $annulation;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;

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
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addInscriptionSortie($this);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeInscriptionSortie($this);
        }

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getNombreParticipants(): ?int
    {
        return count($this->participants);
    }

    public function getEtat(): ?string
    {
        // archivée si date de plus d'un mois
        if (new DateTime('now') > date_add($this->getDateSortie(), date_interval_create_from_date_string(" 1 month"))) {
            return SortieConstants::ETAT_ARCHIVEE;
        }

        if ($this->isAnnulation()) {
            return SortieConstants::ETAT_ANNULEE;
        }

        if (false === $this->isPubliee()) {
            return SortieConstants::ETAT_EN_CREATION;
        }

        $dateNow = new DateTime('now');
    
        if ($this->getDateSortie() < $dateNow && date_add($this->getDateSortie(),date_interval_create_from_date_string($this->getDuree() . " minutes")) > $dateNow) {
            return SortieConstants::ETAT_EN_COURS;
        } else if ($this->getDateSortie() < $dateNow) {
            return SortieConstants::ETAT_PASSE;
        } else {
            if ($this->getDateFinInscription() < $dateNow || $this->getNombreParticipants() > $this->getNombrePlace()) {
                return SortieConstants::ETAT_FERME;
            } else {
                return SortieConstants::ETAT_OUVERT;
            }
        }
    }

    public function isPubliee(): ?bool
    {
        return $this->publiee;
    }

    public function setPubliee(bool $publiee): static
    {
        $this->publiee = $publiee;

        return $this;
    }
}
