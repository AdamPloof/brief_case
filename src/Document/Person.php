<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Service\UploaderHelper;

/**
 * @MongoDB\EmbeddedDocument
 */
class Person
{
    /**
     * @MongoDB\Field(type="string")
     * @Groups({"list_cases"})
     */
    protected $name;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"list_cases"})
     */
    protected $role;

    /**
     * @MongoDB\Field(type="hash")
     * @Groups({"list_cases"})
     */
    protected $traits = [];

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"list_cases"})
     */
    protected $image;

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getRole(): ?string {
        return $this->role;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }

    public function getTraits(): array {
        return $this->traits;
    }

    public function setTraits(array $traits): void {
        $this->traits = $traits;
    }

    public function removeTrait(string $trait): void {
        unset($this->traits[$trait]);
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function setImage(string $image): void {
        $this->image = $image;
    }

    public function getImagePath(): string {
        return UploaderHelper::IMAGE_PATH . '/' . $this->getImage();
    } 
}
