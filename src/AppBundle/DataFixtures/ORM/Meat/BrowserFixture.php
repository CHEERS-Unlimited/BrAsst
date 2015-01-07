<?php
# src/AppBundle/DataFixtures/ORM/Meat/BrowserFixture.php
namespace AppBundle\DataFixtures\ORM\Meat;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Meat\Browser;

class BrowserFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $browserChrome = (new Browser)
            ->setVendor(Browser::VENDOR_GOOGLE)
            ->setName(Browser::BROWSER_CHROME)
            ->setLink(Browser::LINK_CHROME)
            ->setMarketShare(NULL);
        $manager->persist($browserChrome);

        $browserExplorer = (new Browser)
            ->setVendor(Browser::VENDOR_MICROSOFT)
            ->setName(Browser::BROWSER_EXPLORER)
            ->setLink(Browser::LINK_EXPLORER)
            ->setMarketShare(NULL);
        $manager->persist($browserExplorer);

        $browserFirefox = (new Browser)
            ->setVendor(Browser::VENDOR_MOZILLA)
            ->setName(Browser::BROWSER_FIREFOX)
            ->setLink(Browser::LINK_FIREFOX)
            ->setMarketShare(NULL);
        $manager->persist($browserFirefox);

        $browserOpera = (new Browser)
            ->setVendor(Browser::VENDOR_OPERA)
            ->setName(Browser::BROWSER_OPERA)
            ->setLink(Browser::LINK_OPERA)
            ->setMarketShare(NULL);
        $manager->persist($browserOpera);

        $browserSafari = (new Browser)
            ->setVendor(Browser::VENDOR_APPLE)
            ->setName(Browser::BROWSER_SAFARI)
            ->setLink(Browser::LINK_SAFARI)
            ->setMarketShare(NULL);
        $manager->persist($browserSafari);

        $manager->flush();

        $this->addReference('browserChrome', $browserChrome);
        $this->addReference('browserExplorer', $browserExplorer);
        $this->addReference('browserFirefox', $browserFirefox);
        $this->addReference('browserOpera', $browserOpera);
        $this->addReference('browserSafari', $browserSafari);
    }

    public function getOrder()
    {
        return 1;
    }
}