<?php

namespace ZfModule\Repository;

use Doctrine\ORM\EntityRepository;

class Module extends EntityRepository
{

    public function findModulesByName($term, $orderBy = [], $limit = null)
    {
        if (!$term) {
            // return all modules on empty search
            return $this->findAll($limit);
        }

        $dqlQuery = 'SELECT m FROM ZfModule\Entity\Module m WHERE m.name LIKE :term';

        if ($orderBy && count($orderBy) >= 1) {
            $dqlQuery .= ' ORDER BY ' . implode(',', $orderBy);
        }

        $query = $this->getEntityManager()->createQuery($dqlQuery);
        $query->setParameter('term', '%' . $term . '%');
        $query->setMaxResults($limit);

        return $query;
    }

    public function findAll($limit = null, $offset = null)
    {
        $query = $this->getEntityManager()->createQuery('SELECT m FROM ZfModule\Entity\Module m');
        //$query->setFirstResult($offset);
        //$query->setMaxResults($limit);

        return $query;
    }

    public function countTotalModules()
    {
        $query = $this->getEntityManager()->createQuery('SELECT count(m) FROM ZfModule\Entity\Module m');
        return $query;
    }

}
