<?php

namespace StaffBundle\Repository;


use AppBundle\Entity\AbstractResourceEntity;
use AppBundle\Repository\AbstractRepository;

class EmployeeRepository extends AbstractRepository
{
    const FIELD_NAME = 'name';
    const FIELD_SKILLS = 'skills';
    const FIELD_PROJECTS = 'projects';
    const FIELD_POSITIONS = 'positions';

    /**
     * @param array $options
     * @return AbstractResourceEntity[]
     */
    public function getSortedByName(array $options)
    {
        $builder = $this->createQueryBuilder('e');
        $builder
            ->where($builder->expr()->like('e.name', ':name'))
            ->setParameter(':name', $options[self::FIELD_NAME])
        ;
        if (array_key_exists(self::FIELD_PROJECTS, $options)) {
            $builder
                ->join('e.memberships', 'm')
                ->join('m.project', 'p')
                ->andWhere($builder->expr()->in('p.id', $options[self::FIELD_PROJECTS]));
        }
        if (array_key_exists(self::FIELD_SKILLS, $options)) {
            $builder
                ->join('e.technologies', 't')
                ->andWhere($builder->expr()->in('t.id', $options[self::FIELD_SKILLS]));
        }
        if (array_key_exists(self::FIELD_POSITIONS, $options)) {
            $builder
                ->join('e.position', 'position')
                ->andWhere($builder->expr()->in('position.id', $options[self::FIELD_POSITIONS]));
        }
        $this->paginationWrapper($builder, $options);
        $this->sortingWrapper($builder, $options);

        return $builder->getQuery()->getResult();
    }

    public function mostFreeByTechnologies($technologies, $options)
    {
        $builder = $this->createQueryBuilder('e');
        $builder
            ->join('e.technologies', 't')
            ->leftJoin('e.memberships', 'm')
            ->leftJoin('m.project', 'p')
            ->addSelect('count(p.id) as projectCount')
            ->groupBy('e.id')
            ->orderBy('projectCount', 'ASC')
            ->where($builder->expr()->in('t.id', $technologies))
            ->andWhere($builder->expr()->orX(
                $builder->expr()->lt('p.finishDate', ':finish_at'),
                $builder->expr()->isNull('p.finishDate')
            ))
            ->setParameter(':finish_at', new \DateTime('now'))
        ;
        $this->paginationWrapper($builder, $options);

        return $builder->getQuery()->getResult();
    }
}