<?php

namespace App\Model;

class SortieActionModel
{
    private ?string $nom = null;
    private ?string $url = null;

    private function __construct()
    {}

    static public function getSortieActionItem(string $nom, string $url) {
        $model = new SortieActionModel();

        $model->setNom($nom);
        $model->setUrl($url);

        return $model;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

}
