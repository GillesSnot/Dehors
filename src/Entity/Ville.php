<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $nom;

    #[ORM\Column(length: 255)]
    private string $cp;

    /**
     * @var Collection<int, Lieu>
     */
    #[ORM\OneToMany(targetEntity: Lieu::class, mappedBy: 'ville')]
    private Collection $Lieux;

    /**
     * @var Collection<int, Campus>
     */
    #[ORM\OneToMany(targetEntity: Campus::class, mappedBy: 'ville')]
    private Collection $campus;

    public function __construct()
    {
        $this->Lieux = new ArrayCollection();
        $this->campus = new ArrayCollection();
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

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): static
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * @return Collection<int, Lieu>
     */
    public function getLieux(): Collection
    {
        return $this->Lieux;
    }

    public function addLieu(Lieu $lieu): static
    {
        if (!$this->Lieux->contains($lieu)) {
            $this->Lieux->add($lieu);
            $lieu->setVille($this);
        }

        return $this;
    }

    public function removeLieu(Lieu $lieu): static
    {
        if ($this->Lieux->removeElement($lieu)) {
            // set the owning side to null (unless already changed)
            if ($lieu->getVille() === $this) {
                $lieu->setVille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Campus>
     */
    public function getCampus(): Collection
    {
        return $this->campus;
    }

    public function addCampus(Campus $campus): static
    {
        if (!$this->campus->contains($campus)) {
            $this->campus->add($campus);
            $campus->setVille($this);
        }

        return $this;
    }

    public function removeCampus(Campus $campus): static
    {
        if ($this->campus->removeElement($campus)) {
            // set the owning side to null (unless already changed)
            if ($campus->getVille() === $this) {
                $campus->setVille(null);
            }
        }

        return $this;
    }
}
