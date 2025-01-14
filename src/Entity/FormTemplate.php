<?php

namespace App\Entity;

use App\Repository\FormTemplateRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: FormTemplateRepository::class)]
class FormTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Name cannot be empty.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Name cannot exceed 255 characters."
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Description cannot exceed 255 characters."
    )]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'formTemplate', targetEntity: FormField::class, cascade: ['persist', 'remove'])]
    private Collection $fields;

    /**
     * @var Collection<int, FormAnswer>
     */
    #[ORM\OneToMany(targetEntity: FormAnswer::class, mappedBy: 'formTemplate', orphanRemoval: true)]
    private Collection $answers;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(FormField $field): self
    {
        if (!$this->fields->contains($field)) {
            $this->fields[] = $field;
            $field->setFormTemplate($this);
        }

        return $this;
    }

    public function removeField(FormField $field): self
    {
        if ($this->fields->removeElement($field)) {
            if ($field->getFormTemplate() === $this) {
                $field->setFormTemplate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FormAnswer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(FormAnswer $answer): static
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setFormTemplate($this);
        }

        return $this;
    }

    public function removeAnswer(FormAnswer $answer): static
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getFormTemplate() === $this) {
                $answer->setFormTemplate(null);
            }
        }

        return $this;
    }

}
