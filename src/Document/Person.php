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

    public function setTraits(string $strTraits): void {
        // Traits are submitted as strings of key:value pairs separated by commas
        // Ex: 'height:tall,weight:160lb,disposition:sunny'
        $traits = explode(',', $strTraits);
        foreach ($traits as $trait) {
            $hash = explode(':', $trait);
            $this->traits[$hash[0]] = $hash[1];
        }
    }

    public function removeTrait(string $trait): void {
        unset($this->traits[$trait]);
    }
}
