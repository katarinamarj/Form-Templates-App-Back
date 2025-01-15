<?php

namespace App\Entity;

use App\Repository\FormFieldRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: FormFieldRepository::class)]
class FormField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Label cannot be empty.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Label cannot exceed 255 characters."
    )]
    private ?string $label = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Type is required.")]
    #[Assert\Choice(
        choices: ["text", "checkbox", "dropdown"],
        message: "Invalid type. Allowed values: text, checkbox, dropdown."
    )]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $options = null;

    #[ORM\ManyToOne(targetEntity: FormTemplate::class, inversedBy: 'fields')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?FormTemplate $formTemplate = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(?string $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getFormTemplate(): ?FormTemplate
    {
        return $this->formTemplate;
    }

    public function setFormTemplate(?FormTemplate $formTemplate): static
    {
        $this->formTemplate = $formTemplate;

        return $this;
    }


    public function toArray(): array
    {
        return [
           'id' => $this->getId(),
           'label' => $this->getLabel(),
           'type' => $this->getType(),
           'options' => $this->getOptions()
        ];
    }

    public static function createFromData(array $data, FormTemplate $formTemplate): self
    {
        $field = new self();
        $field->setLabel($data['label'] ?? '');
        $field->setType($data['type'] ?? '');
        $field->setOptions($data['options'] ?? null);
        $field->setFormTemplate($formTemplate);

        return $field;
    }

    public function updateFromData(array $data): self
    {
        if (isset($data['label'])) {
           $this->setLabel($data['label']);
        }

        if (isset($data['type'])) {
           $this->setType($data['type']);
        }

        if (isset($data['options'])) {
           $this->setOptions($data['options']);
        } 

       return $this;
    }


}
