<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Repository\ClotheRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ApiResource(
 *     attributes={
 *          "order"={"createdAt"},
 *          "normalization_context"={"groups"={"clothe"}}
 *     },
 *     collectionOperations={
 *         "get",
 *         "post"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *         "get",
 *         "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *         "put"={"security"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 * @ApiFilter(
 *     SearchFilter::class, properties={"id": "exact", "popularity": "exact", "categories.name": "exact", "sizes": "partial", "colors": "partial", "brand": "exact", "title": "partial"}
 * )
 * @ApiFilter(
 *     RangeFilter::class, properties={"price"}
 * )
 * @ApiFilter(
 *     BooleanFilter::class, properties={"isAvailable"}
 * )
 * @ApiFilter(
 *     OrderFilter::class, properties={"price", "popularity", "createdAt"}, arguments={"orderParameterName"="order"}
 * )
 * @ORM\Entity(repositoryClass=ClotheRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Clothe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Groups({"clothe"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Groups({"clothe"})
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     * 
     * @Groups({"clothe"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=25)
     * 
     * @Groups({"clothe"})
     */
    private $popularity;

    /**
     * @ORM\Column(type="integer")
     * 
     * @Groups({"clothe"})
     */
    private $impacts = 0;

    /**
     * @ORM\Column(type="boolean")
     * 
     * @Groups({"clothe"})
     */
    private $isRecommended;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, mappedBy="clothes")
     * 
     * @Groups({"clothe"})
     */
    private $categories;

    /**
     * @var MediaObject|null
     *
     * @ORM\ManyToOne(targetEntity=MediaObject::class)
     * @ORM\JoinColumn(nullable=true)
     * @ApiProperty(iri="http://schema.org/image")
     * 
     * @Groups({"clothe"})
     */
    public $image;

    /**
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=true)
     *
     * @Groups({"clothe"})
     */
    public $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime_immutable", nullable=true)
     *
     * @Groups({"clothe"})
     */
    public $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="myClothes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"clothe"})
     */
    private $createdBy;

    /**
     * @Groups({"clothe"})
     */
    public $percentage;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"clothe"})
     */
    private $link;

    /**
     * @ORM\Column(type="array")
     * @Groups({"clothe"})
     */
    private $sizes = [];

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"clothe"})
     */
    private $brand;

    /**
     * @ORM\Column(type="array")
     * @Groups({"clothe"})
     */
    private $colors = [];

    /**
     * @ORM\ManyToMany(targetEntity=UserProfile::class, inversedBy="favs")
     */
    private $userProfileFavs;

    /**
     * @ORM\Column(type="boolean")
     * 
     * @Groups({"clothe"})
     */
    private $isAvailable = true;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->userProfiles = new ArrayCollection();
        $this->userProfileFavs = new ArrayCollection();
    }

    /**
     * Gets triggered only on insert.
     *
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->updatedAt = $this->createdAt;
        $this->popularity = 'Tendencias';
    }

    /**
     * Gets triggered every time on update.
     *
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTimeImmutable('now');
        if ($this->getPercentage() < 25) {
            $this->popularity = '4';
        } else if ($this->getPercentage() < 50) {
            $this->popularity = '3';
        } else if ($this->getPercentage() < 75) {
            $this->popularity = '2';
        } else {
            $this->popularity = '1';
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPopularity(): ?string
    {
        return $this->popularity;
    }

    public function setPopularity(string $popularity): self
    {
        $this->popularity = $popularity;

        return $this;
    }

    public function getImpacts(): ?int
    {
        return $this->impacts;
    }

    public function setImpacts(int $impacts): self
    {
        $this->impacts = $impacts;

        return $this;
    }

    public function getIsRecommended(): ?bool
    {
        return $this->isRecommended;
    }

    public function setIsRecommended(bool $isRecommended): self
    {
        $this->isRecommended = $isRecommended;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addClothes($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeClothes($this);
        }

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getcreatedAt(): String
    {
        return $this->createdAt->format('d-m-Y H:i:s');
    }

    public function getPercentage(): Float
    {
        $percentage = 0;
        $this->getIsRecommended() ? $percentage+=35 : $percentage+=20;
        if ($this->getImpacts() > 0 && $this->getImpacts() <= 5) {
            $percentage+=20;
        } else if ($this->getImpacts() > 5 && $this->getImpacts() <= 10) {
            $percentage+=35;
        } else if ($this->getImpacts() > 10) {
            $percentage+=50;
        }
        if (count($this->getUserProfileFavs()) > 0 && count($this->getUserProfileFavs()) <= 2) {
            $percentage+=5;
        } else if (count($this->getUserProfileFavs()) > 2 && count($this->getUserProfileFavs()) <= 4) {
            $percentage+=10;
        } else if (count($this->getUserProfileFavs()) > 4) {
            $percentage+=15;
        }
        return $percentage;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getSizes(): ?array
    {
        return $this->sizes;
    }

    public function setSizes(array $sizes): self
    {
        $this->sizes = $sizes;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getColors(): ?array
    {
        return $this->colors;
    }

    public function setColors(array $colors): self
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * @return Collection|UserProfile[]
     */
    public function getUserProfileFavs(): Collection
    {
        return $this->userProfileFavs;
    }

    public function addUserProfileFav(UserProfile $userProfileFav): self
    {
        if (!$this->userProfileFavs->contains($userProfileFav)) {
            $this->userProfileFavs[] = $userProfileFav;
        }

        return $this;
    }

    public function removeUserProfileFav(UserProfile $userProfileFav): self
    {
        $this->userProfileFavs->removeElement($userProfileFav);

        return $this;
    }

    public function getIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }
}
