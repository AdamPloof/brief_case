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
}