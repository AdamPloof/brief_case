<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class CaseFileRepository extends DocumentRepository
{
    public function findAllOrderByDate() {
        return $this->createQueryBuilder()
            ->sort('date', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function getRelatedCaseObjects($caseId) {
        $relatedCaseIds = $this->find($caseId)->getRelatedCases();

        if (!isset($relatedCaseIds)) {
            return null;
        }

        return $this->createQueryBuilder()
            ->field('_id')->in($relatedCaseIds)
            ->getQuery()
            ->execute();
    }
}
