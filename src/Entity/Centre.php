<?php

namespace App\Entity;

use App\Repository\CentreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use http\Message;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CentreRepository::class)]
class Centre
{
    #[ORM\Id]

    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[groups ("post:read")]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[groups ("post:read")]

    #[Assert\NotBlank (message:'champ obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 500,
        minMessage: 'insuffisant {{ limit }}',
        maxMessage: 'trop long {{ limit }} ',
    )]

    private ?string $nomCentre = null;

    #[ORM\Column(length: 255)]
    #[groups ("post:read")]

    #[Assert\NotBlank (message:'champ obligatoire')]

    private ?string $adresseCentre = null;

    #[ORM\Column(length: 255)]
    #[groups ("post:read")]

    #[Assert\NotBlank (message:'champ obligatoire')]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    /*
#[Assert\Regex(pattern = "/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/",
    message = "L'adresse email doit être en minuscules.")]
*/
    private ?string $emailCentre = null;

    #[ORM\Column]
    #[groups ("post:read")]

    #[Assert\NotBlank (message:'champ obligatoire')]
    #[Assert\Length(
        min: 8,
        max: 8,
        minMessage: 'numero telephone non valide',
        maxMessage: 'numero telephone non valide',
    )]
    private ?int $telephoneCentre = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[groups ("post:read")]
    // #[Assert\Url]
    private ?string $siteWebCentre = null;

    #[ORM\OneToMany(mappedBy: 'idCentre', targetEntity: Formation::class, orphanRemoval: true )]

    private Collection $formations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagecentre = null;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCentre(): ?string
    {
        return $this->nomCentre;
    }

    public function setNomCentre(string $nomCentre): self
    {
        $this->nomCentre = $nomCentre;

        return $this;
    }

    public function getAdresseCentre(): ?string
    {
        return $this->adresseCentre;
    }

    public function setAdresseCentre(string $adresseCentre): self
    {
        $this->adresseCentre = $adresseCentre;

        return $this;
    }

    public function getEmailCentre(): ?string
    {
        return $this->emailCentre;
    }

    public function setEmailCentre(string $emailCentre): self
    {
        $this->emailCentre = $emailCentre;

        return $this;
    }

    public function getTelephoneCentre(): ?int
    {
        return $this->telephoneCentre;
    }

    public function setTelephoneCentre(int $telephoneCentre): self
    {
        $this->telephoneCentre = $telephoneCentre;

        return $this;
    }

    public function getSiteWebCentre(): ?string
    {
        return $this->siteWebCentre;
    }

    public function setSiteWebCentre(?string $siteWebCentre): self
    {
        $this->siteWebCentre = $siteWebCentre;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setIdCentre($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getIdCentre() === $this) {
                $formation->setIdCentre(null);
            }
        }

        return $this;
    }
    public function  __toString(): string
    {
        return $this->getNomCentre();
    }

    public function getImagecentre(): ?string
    {
        return $this->imagecentre;
    }

    public function setImagecentre(?string $imagecentre): self
    {
        $this->imagecentre = $imagecentre;

        return $this;
    }
}
