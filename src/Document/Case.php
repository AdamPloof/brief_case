<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

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
     * @MongoDB\Field(type="date)
     */
    protected $date;

    /**
     * @MongoDB\EmbedOne(targetDocument="Person::class")
     */
    protected $primary_person;

    /**
     * @MongoDB\EmbedMany(targetDocument="Person::class")
     */
    protected $others_involved;
}

/**
 * @MongoDB\EmbeddedDocument
 */
class Person
{
    /**
     * @MondgoDB\Field(type="string")
     */
    protected $name;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $role;

    /**
     * @MongoDB\Field(type="hash")
     */
    protected $traits;
}