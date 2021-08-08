<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubResource;
use App\Repository\UserProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=UserProfileRepository::class)
 */
class UserProfile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="userProfile", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Clothe::class, mappedBy="userProfileFavs")
     * @ApiSubResource
     */
    private $favs;

    public function __construct()
    {
        $this->favs = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getUserProfile() !== $this) {
            $user->setUserProfile($this);
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Clothe[]
     */
    public function getFavs(): Collection
    {
        return $this->favs;
    }

    public function addFav(Clothe $fav): self
    {
        if (!$this->favs->contains($fav)) {
            $this->favs[] = $fav;
            $fav->addUserProfileFav($this);
        }

        return $this;
    }

    public function removeFav(Clothe $fav): self
    {
        if ($this->favs->removeElement($fav)) {
            $fav->removeUserProfileFav($this);
        }

        return $this;
    }
}
