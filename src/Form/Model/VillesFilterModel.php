<?php

namespace App\Form\Model;

class VillesFilterModel
{
    private ?string $recherche = null;

    public function getRecherche(): ?string
    {
        return $this->recherche;
    }

    public function setRecherche(?string $recherche): static
    {
        $this->recherche = $recherche;

        return $this;
    }
}
