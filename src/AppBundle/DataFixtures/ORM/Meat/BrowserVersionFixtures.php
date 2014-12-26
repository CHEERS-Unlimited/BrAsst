<?php
# C:\wamp\www\BrAsst\src\AppBundle\DataFixtures\ORM\Meat\BrowserVersionFixtures.php
namespace AppBundle\DataFixtures\ORM\Meat;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Meat\BrowserVersion;

class OperatingSystemFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        //Don't you need that!

        $systemWindowsChrome = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_WINDOWS)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserChrome')));
        $manager->persist($systemWindowsChrome);

        $systemWindowsFirefox = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_WINDOWS)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserFirefox')));
        $manager->persist($systemWindowsFirefox);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}