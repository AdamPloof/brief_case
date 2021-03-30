<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Service\UploaderHelper;
use App\Document\Person;
use App\Repository\CaseFileRepository;

/**
 * @MongoDB\Document(db="BriefCase", collection="cases_tab", repositoryClass=CaseFileRepository::class)
 */
class CaseFile
{
    /**
     * @MongoDB\Id
     * @Groups({"list_cases"})
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"list_cases"})
     */
    protected $description;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"list_cases"})
     */
    protected $category;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"list_cases"})
     */
    protected $summary;

    /**
     * @MongoDB\Field(type="date")
     * @Groups({"list_cases"})
     */
    protected $date;

    /**
     * @MongoDB\EmbedOne(targetDocument=Person::class)
     * @Assert\Valid
     * @Groups({"list_cases"})
     */
    protected $primary_person;

    /**
     * @MongoDB\EmbedMany(targetDocument=Person::class, strategy="setArray")
     * @Assert\Valid
     * @Groups({"list_cases"})
     */
    protected $associated_persons;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"list_cases"})
     */
    protected $video;

    /**
     * @MongoDB\Field(type="collection")
     */
    public $casesRelatedWithThis = [];

    /**
     * @MongoDB\Field(type="collection")
     */
    public $related_cases = [];

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

    public function getCategory(): ?string {
        return $this->category;
    }

    public function setCategory(string $category): void {
        $this->category = $category;
    }

    public function getSummary(): ?string {
        return $this->summary;
    }

    public function setSummary(string $summary): void {
        $this->summary = $summary;
    }

    public function getDate(): ?\DateTime {
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
        $personsToKeep = array();
        foreach ($this->associated_persons as $key => $person) {
            if ($person != $associatedPerson) {
                $personsToKeep[] = $person;
            }
            unset($this->associated_persons[$key]);
        }
        
        foreach ($personsToKeep as $person) {
            $this->addAssociatedPerson($person);
        }
    }

    public function getRelatedCases(): ?array {
        return $this->related_cases;
    }

    public function setRelatedCases(array $relatedCase): void {
        $this->related_cases = $relatedCase;
    }

    public function removeRelatedCase(string $relatedCase) {
        unset($this->related_cases[$relatedCase]);
    }

    public function getVideo(): ?string {
        return $this->video;
    }

    public function setVideo(string $video): void {
        $this->video = $video;
    }

    public function getVideoPath() {
        return UploaderHelper::VIDEO_PATH . '/' . $this->getVideo();
    }

    public function getAssociatedPersonByName($name) {
        $persons = $this->getAssociatedPersons();
        foreach ($persons as $person) {
            if ($person->getName() == $name) {
                return $person;
            }
        }
        return null;
    }
}
