<?php
# src/AppBundle/Entity/Repository/Meat/BrowserRepository.php
namespace AppBundle\Entity\Repository\Meat;

use Doctrine\ORM\EntityRepository;

class BrowserRepository extends EntityRepository
{
    public function findByName(array $nameList)
    {
        $query = $this->createQueryBuilder('browser')
            ->select('browser')
            ->where('browser.name IN (:nameList)')
            ->setParameter(':nameList', $nameList)
            ->getQuery();

        return $query->getResult();
    }
}