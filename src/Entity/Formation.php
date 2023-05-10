<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[groups ("post:read")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:'champ obligatoire')]
    #[groups ("post:read")]
    private ?string $nomFormation = null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank (message:'champ obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 500,
        minMessage: 'insuffisant {{ limit }}',
        maxMessage: 'trop long {{ limit }} ',
    )]
    #[groups ("post:read")]
    private ?string $descriptionFormation = null;

    #[ORM\Column]
    #[Assert\NotBlank (message:'champ obligatoire')]
    #[Assert\Positive]
    #[groups ("post:read")]

    private ?float $coutFormation = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank (message:'champ obligatoire')]
    #[Assert\Positive]
    #[groups ("post:read")]
    private ?int $NombreDePlace = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank (message:'champ obligatoire')]
    #[Assert\Positive]
    #[groups ("post:read")]
    private ?string $duree = null;

    #[ORM\ManyToOne(inversedBy: 'formations')]
    private ?CategorieFormation $idCategorieFormation = null;

    #[ORM\ManyToOne(inversedBy: 'formations')]
    // #[groups ("post:read")]
    private ?Centre $idCentre = null;

    #[ORM\OneToMany(mappedBy: 'formations', targetEntity: InscriptionFormation::class)]
    private Collection $inscriptionFormations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iamgeformation = null;

    public function __construct()
    {
        $this->inscriptionFormations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFormation(): ?string
    {
        return $this->nomFormation;
    }

    public function setNomFormation(string $nomFormation): self
    {
        $this->nomFormation = $nomFormation;

        return $this;
    }

    public function getDescriptionFormation(): ?string
    {
        return $this->descriptionFormation;
    }

    public function setDescriptionFormation(string $descriptionFormation): self
    {
        $this->descriptionFormation = $descriptionFormation;

        return $this;
    }

    public function getCoutFormation(): ?float
    {
        return $this->coutFormation;
    }

    public function setCoutFormation(float $coutFormation): self
    {
        $this->coutFormation = $coutFormation;

        return $this;
    }

    public function getNombreDePlace(): ?int
    {
        return $this->NombreDePlace;
    }

    public function setNombreDePlace(?int $NombreDePlace): self
    {
        $this->NombreDePlace = $NombreDePlace;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getIdCategorieFormation(): ?CategorieFormation
    {
        return $this->idCategorieFormation;
    }

    public function setIdCategorieFormation(?CategorieFormation $idCategorieFormation): self
    {
        $this->idCategorieFormation = $idCategorieFormation;

        return $this;
    }

    public function getIdCentre(): ?Centre
    {
        return $this->idCentre;
    }

    public function setIdCentre(?Centre $idCentre): self
    {
        $this->idCentre = $idCentre;

        return $this;
    }

    /**
     * @return Collection<int, InscriptionFormation>
     */
    public function getInscriptionFormations(): Collection
    {
        return $this->inscriptionFormations;
    }

    public function addInscriptionFormation(InscriptionFormation $inscriptionFormation): self
    {
        if (!$this->inscriptionFormations->contains($inscriptionFormation)) {
            $this->inscriptionFormations->add($inscriptionFormation);
            $inscriptionFormation->setFormations($this);
        }

        return $this;
    }

    public function removeInscriptionFormation(InscriptionFormation $inscriptionFormation): self
    {
        if ($this->inscriptionFormations->removeElement($inscriptionFormation)) {
            // set the owning side to null (unless already changed)
            if ($inscriptionFormation->getFormations() === $this) {
                $inscriptionFormation->setFormations(null);
            }
        }

        return $this;
    }
    public function  __toString(): string
    {
        return $this->getNomFormation();
    }

    public function getIamgeformation(): ?string
    {
        return $this->iamgeformation;
    }

    public function setIamgeformation(?string $iamgeformation): self
    {
        $this->iamgeformation = $iamgeformation;

        return $this;
    }
}
