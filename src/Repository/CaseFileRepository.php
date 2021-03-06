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
}
