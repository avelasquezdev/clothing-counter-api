<?php

namespace App\Entity;

use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 *  @ApiResource(
 *     attributes={
 *          "order"={"email"},
 *          "normalization_context"={"groups"={"user"}}
 *     },
 *     collectionOperations={
 *         "get",
 *         "post"
 *     },
 *     itemOperations={
 *         "get"={"access_control"="(is_granted('ROLE_USER') and user.getId() == object.getId()) or is_granted('ROLE_ADMIN')"},
 *         "delete"={"access_control"="is_granted('ROLE_ADMIN')"},
 *         "put"={"access_control"="is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and user.getId() == object.getId())"}
 *     }
 * )
 * @ApiFilter(
 *     SearchFilter::class, properties={"email": "partial"}
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * 
     * @Groups({"user"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * 
     * @Groups({"user"})
     */
    private $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Groups({"user"})
     */
    private $username;

    /**
     * @Groups({"user"})
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity=UserProfile::class, inversedBy="user", cascade={"persist", "remove"})
     * @ApiSubresource
     * @Groups({"user"})
     */
    private $userProfile;

    public function __construct()
    {
        $this->favoriteClothes = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getName(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(UserProfile $userProfile): self
    {
        $this->userProfile = $userProfile;

        return $this;
    }
}
