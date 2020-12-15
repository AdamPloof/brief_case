<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
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
    protected $others_involved;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $video;

    public function __construct() {
        $this->others_involved = new ArrayCollection();
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

    public function getOthersInvolved(): Collection {
        return $this->others_involved;
    }

    public function addOtherInvolved(Person $OtherInvolved): void {
        $this->others_involved[] = $OtherInvolved;
    }

    public function getVideo(): ?string {
        return $this->video;
    }

    public function setVideo(string $video): void {
        $this->video = $video;
    }
}
