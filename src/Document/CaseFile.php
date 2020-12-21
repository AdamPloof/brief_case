<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

use App\Document\Person;

/**
 * @MongoDB\Document(db="BriefCase", collection="cases_tab")
 */
class CaseFile
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $description;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $summary;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $date;

    /**
     * @MongoDB\EmbedOne(targetDocument="Person::class")
     * @Assert\Valid
     */
    protected $primary_person;

    /**
     * @MongoDB\EmbedMany(targetDocument="Person::class")
     * @Assert\Valid
     */
    protected $associated_persons;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $video;

    public function __construct() {
        $this->associated_persons = new ArrayCollection();
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function getSummary(): ?string {
        return $this->summary;
    }

    public function setSummary(string $summary): void {
        $this->summary = $summary;
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(\DateTime $date): void {
        $this->date = $date;
    }

    public function getPrimaryPerson(): ?Person {
        return $this->primary_person;
    }

    public function setPrimaryPerson(Person $PrimaryPerson): void {
        $this->primary_person = $PrimaryPerson;
    }

    public function getAssociatedPersons(): Collection {
        return $this->associated_persons;
    }

    public function addAssociatedPerson(Person $associatedPerson): void {
        $this->associated_persons[] = $associatedPerson;
    }

    public function removeAssociatedPerson(Person $associatedPerson) {
        // TODO: So far as I know, this won't actually work, 
        // this is just a placeholder since there must be an add and remove method for associated persons
        unset($this->associated_persons[$associatedPerson]);
    }

    public function getVideo(): ?string {
        return $this->video;
    }

    public function setVideo(string $video): void {
        $this->video = $video;
    }
}
