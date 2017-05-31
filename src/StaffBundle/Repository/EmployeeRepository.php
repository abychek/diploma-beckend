<?php

namespace StaffBundle\Repository;


use AppBundle\Entity\AbstractResourceEntity;
use AppBundle\Repository\AbstractRepository;

class EmployeeRepository extends AbstractRepository
{
    const FIELD_NAME = 'name';

    /**
     * @param array $options
     * @return AbstractResourceEntity[]
     */
    public function getSortedByName(array $options)
    {
        return $this->getSortedBy(self::FIELD_NAME, $options);
    }
}