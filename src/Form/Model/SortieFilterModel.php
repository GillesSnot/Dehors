<?php

namespace App\Form\Model;

use App\Entity\Campus;
use DateTimeInterface;

class SortieFilterModel
{
    private ?Campus $campus = null;
    private ?string $recherche = null;
    private ?DateTimeInterface $dateDebut = null;
    private ?DateTimeInterface $dateFin = null;
    private ?bool $organisateur = null;
    private ?bool $inscrit = null;
    private ?bool $nonInscrit = null;
    private ?bool $passee = null;

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getRecherche(): ?string
    {
        return $this->recherche;
    }

    public function setRecherche(?string $recherche): static
    {
        $this->recherche = $recherche;

        return $this;
    }

    public function getDateDebut(): ?DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function isOrganisateur(): ?bool
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?bool $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function isInscrit(): ?bool
    {
        return $this->inscrit;
    }

    public function setInscrit(?bool $inscrit): static
    {
        $this->inscrit = $inscrit;

        return $this;
    }

    public function isNonInscrit(): ?bool
    {
        return $this->nonInscrit;
    }

    public function setNonInscrit(?bool $nonInscrit): static
    {
        $this->nonInscrit = $nonInscrit;

        return $this;
    }

    public function isPassee(): ?bool
    {
        return $this->passee;
    }

    public function setPassee(?bool $passee): static
    {
        $this->passee = $passee;

        return $this;
    }
}
