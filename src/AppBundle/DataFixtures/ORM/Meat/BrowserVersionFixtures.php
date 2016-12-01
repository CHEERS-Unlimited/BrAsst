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
        //browserChrome

        $systemWindowsChrome = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_WINDOWS)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserChrome')));
        $manager->persist($systemWindowsChrome);

        $systemLinuxChrome = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_LINUX)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserChrome')));
        $manager->persist($systemLinuxChrome);

        $systemMacChrome = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_MAC)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserChrome')));
        $manager->persist($systemMacChrome);

        //browserExplorer

        $systemWindowsExplorer = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_WINDOWS)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserExplorer')));
        $manager->persist($systemWindowsExplorer);

        //browserFirefox

        $systemWindowsFirefox = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_WINDOWS)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserFirefox')));
        $manager->persist($systemWindowsFirefox);

        $systemLinuxFirefox = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_LINUX)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserFirefox')));
        $manager->persist($systemLinuxFirefox);

        $systemMacFirefox = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_MAC)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserFirefox')));
        $manager->persist($systemMacFirefox);

        //browserOpera

        $systemWindowsOpera = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_WINDOWS)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserOpera')));
        $manager->persist($systemWindowsOpera);

        $systemLinuxOpera = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_LINUX)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserOpera')));
        $manager->persist($systemLinuxOpera);

        $systemMacOpera = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_MAC)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserOpera')));
        $manager->persist($systemMacOpera);

        //browserSafari

        $systemMacSafari = (new BrowserVersion)
            ->setName(BrowserVersion::SYSTEM_MAC)
            ->setVersion(NULL)
            ->setBrowser($manager->merge($this->getReference('browserSafari')));
        $manager->persist($systemMacSafari);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}