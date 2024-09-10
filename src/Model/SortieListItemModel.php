<?php

namespace App\Model;

class SortieListItemModel
{
    private ?int $id = null;
    private ?string $nom = null;
    private ?string $dateSortie = null;
    private ?string $dateFinInscription = null;
    private ?string $inscritsPlaces = null;
    private ?string $organisateur = null;
    private ?int $organisateurId = null;
    private ?string $etat = null;
    private ?bool $inscrit = null;
    private ?array $actions = [];

    private function __construct()
    {}

    static public function getSortieListItem($sortie, $loggedUser) {
        $model = new SortieListItemModel();

        $model->setId($sortie->getId());
        $model->setNom($sortie->getNom());
        $model->setDateSortie($sortie->getDateSortie()->format('d-m-Y H:i'));
        $model->setDateFinInscription($sortie->getDateFinInscription()->format('d-m-Y'));
        $model->setInscritsPlaces($sortie->getNombreParticipants() . '/' . $sortie->getNombrePlace());
        $model->setOrganisateur($sortie->getOrganisateur()->getPrenom() . ' ' . $sortie->getOrganisateur()->getNom());
        $model->setOrganisateurId($sortie->getOrganisateur()->getId());
        $model->setEtat($sortie->getEtat());
        $model->setInscrit($sortie->getParticipants()->contains($loggedUser));

        return $model;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
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

    public function getDateSortie(): ?string
    {
        return $this->dateSortie;
    }

    public function setDateSortie(?string $dateSortie): self
    {
        $this->dateSortie = $dateSortie;
        return $this;
    }

    public function getDateFinInscription(): ?string
    {
        return $this->dateFinInscription;
    }

    public function setDateFinInscription(?string $dateFinInscription): self
    {
        $this->dateFinInscription = $dateFinInscription;
        return $this;
    }

    public function getInscritsPlaces(): ?string
    {
        return $this->inscritsPlaces;
    }

    public function setInscritsPlaces(?string $inscritsPlaces): self
    {
        $this->inscritsPlaces = $inscritsPlaces;
        return $this;
    }

    public function getOrganisateur(): ?string
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?string $organisateur): self
    {
        $this->organisateur = $organisateur;
        return $this;
    }

    public function getOrganisateurId(): ?int
    {
        return $this->organisateurId;
    }

    public function setOrganisateurId(?int $organisateurId): self
    {
        $this->organisateurId = $organisateurId;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    public function getInscrit(): ?bool
    {
        return $this->inscrit;
    }

    public function setInscrit(?bool $inscrit): self
    {
        $this->inscrit = $inscrit;
        return $this;
    }

    public function addAction(?SortieActionModel $action): self
    {
        array_push($this->actions, $action);
        return $this;
    }

    public function getActions(): ?array
    {
        return $this->actions;
    }

    public function setActions(?array $actions): self
    {
        $this->actions = $actions;
        return $this;
    }

}
