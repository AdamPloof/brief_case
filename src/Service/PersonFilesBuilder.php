<?php

namespace App\Service;

use App\Document\CaseFile;
use App\Document\Person;

use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Compiles a list of cases grouped by person
 */
class PersonFilesBuilder
{
    protected $dm;
    public function __construct(DocumentManager $dm) {
        $this->dm = $dm;
    }

    public function getAllPersonFiles() {
        $cases = $this->dm->createQueryBuilder(CaseFile::class)
            ->select('id', 'primary_person', 'associated_persons')
            ->readOnly()
            ->getQuery()
            ->execute();

        $personFiles = array();
        foreach ($cases as $case) {
            $primaryPerson = $case->getPrimaryPerson()->getName();

            if (isset($personFiles[$primaryPerson])) {
                $personFiles[$primaryPerson]['primary'][] = $case->getId(); 
            } else {
                $personFiles[$primaryPerson] = array(
                    'primary' => array(
                        $case->getId()
                    ),
                    'associated' => array(),
                );
            }

            $assocPersons = $case->getAssociatedPersons();
            foreach ($assocPersons as $assocPerson) {
                $assocName = $assocPerson->getName();

                if (isset($personFiles[$assocName])) {
                    $personFiles[$assocName]['associated'][] = $case->getId(); 
                } else {
                    $personFiles[$assocName] = array(
                        'primary' => array(),
                        'associated' => array(
                            $case->getId()
                        ),
                    );
                }
            }
        }

        return $personFiles;
    }

    public function getPersonFiles($name) {
        $qb = $this->dm->createQueryBuilder(CaseFile::class);
        
        $cases = $qb->addOr($qb->expr()->field('primary_person.name')->equals($name))
            ->addOr($qb->expr()->field('associated_persons.name')->equals($name))
            ->readOnly()
            ->getQuery()
            ->execute();

        $files = array(
            'person' => null,
            'primary' => array(),
            'associated' => array()
        );

        // TODO: The person info currently comes from taking the first matching person document
        // It would be better if it gathered a list of all traits and images into a master info list
        foreach ($cases as $case) {
            $primaryPerson = $case->getPrimaryPerson();
            if ($primaryPerson->getName() == $name) {
                $files['primary'][] = $case;

                if (!$files['person']) {
                    $files['person'] = $primaryPerson;
                }
                continue;
            }

            foreach ($case->getAssociatedPersons() as $person) {
                if ($person->getName() == $name) {
                    $files['associated'][] = $case;

                    if (!$files['person']) {
                        $files['person'] = $person;
                    }
                    continue;
                }
            }
        }

        return $files;
    }
}
