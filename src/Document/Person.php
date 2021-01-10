<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class Person
{
    /**
     * @MongoDB\Field(type="string")
     */
    protected $name;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $role;

    /**
     * @MongoDB\Field(type="hash")
     */
    protected $traits = [];

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
}
