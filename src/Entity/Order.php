<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Attribute\Groups;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;

#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    forceEager: false,
    operations: [
        new GetCollection(security: "is_granted('ROLE_SERVEUR') or is_granted('ROLE_PATRON') or is_granted('ROLE_BARMAN')", securityMessage: 'You are not allowed to get orders'),
        new Get(security: "is_granted('ROLE_PATRON') or is_granted('ROLE_BARMAN') or is_granted('ROLE_SERVEUR')", securityMessage: 'You are not allowed to get orders'),
        new Post(security: "is_granted('ROLE_SERVEUR')"),
        new Patch(security: "(is_granted('ROLE_BARMAN') and object.getStatus() !== 'paid' and object.getBarman() == user) or is_granted('ROLE_SERVEUR)", securityMessage: "You are not to update the order to 'PAID' or assign it to someone else"),
        new Delete(security: "is_granted('ROLE_BARMAN') or is_granted('ROLE_SERVEUR')")
    ]
)]
#[ApiFilter(DateFilter::class, properties: ['createdDate'])]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('read')]
    private ?\DateTimeInterface $createdDate = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?int $tableNumber = null;

    /**
     * @var Collection<int, Drink>
     */
    #[ORM\ManyToMany(targetEntity: Drink::class, inversedBy: 'orders')]
    #[Groups(['read', 'write'])]
    private Collection $drinks;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?User $waiter = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?User $barman = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    private ?string $status = null;

    public function __construct()
    {
        $this->drinks = new ArrayCollection();
        $this->createdDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): static
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getTableNumber(): ?int
    {
        return $this->tableNumber;
    }

    public function setTableNumber(int $tableNumber): static
    {
        $this->tableNumber = $tableNumber;

        return $this;
    }

    /**
     * @return Collection<int, Drink>
     */
    public function getDrinks(): Collection
    {
        return $this->drinks;
    }

    public function addDrink(Drink $drink): static
    {
        if (!$this->drinks->contains($drink)) {
            $this->drinks->add($drink);
        }

        return $this;
    }

    public function removeDrink(Drink $drink): static
    {
        $this->drinks->removeElement($drink);

        return $this;
    }

    public function getWaiter(): ?User
    {
        return $this->waiter;
    }

    public function setWaiter(?User $waiter): static
    {
        $this->waiter = $waiter;

        return $this;
    }

    public function getBarman(): ?User
    {
        return $this->barman;
    }

    public function setBarman(?User $barman): static
    {
        $this->barman = $barman;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $allowedStatuses = ['doing', 'ready', 'paid'];

        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException(sprintf('Invalid status "%s". Allowed statuses are %s.', $status, implode(', ', $allowedStatuses)));
        }

        $this->status = $status;

        return $this;
    }
}
