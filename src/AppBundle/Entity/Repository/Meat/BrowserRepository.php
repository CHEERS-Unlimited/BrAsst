<?php
# src/AppBundle/Entity/Repository/Meat/BrowserRepository.php
namespace AppBundle\Entity\Repository\Meat;

use Doctrine\ORM\EntityRepository;

class BrowserRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy([], ['marketShare' => 'DESC']);
    }

    public function findByName(array $nameList)
    {
        $query = $this->createQueryBuilder('browser')
            ->select('browser, browserVersion')
            ->join('browser.browserVersion', 'browserVersion')
            ->where('browser.name IN (:nameList)')
            ->setParameter(':nameList', $nameList)
            ->getQuery();

        return $query->getResult();
    }
}