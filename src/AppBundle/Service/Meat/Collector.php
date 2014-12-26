<?php
# src/AppBundle/Service/Meat/Collector.php
namespace AppBundle\Service\Meat;

use Symfony\Component\DomCrawler\Crawler;

use Doctrine\ORM\EntityManager;

use Goutte\Client;

use AppBundle\Entity\Meat\Browser,
    AppBundle\Entity\Meat\BrowserVersion;

class Collector
{
    private $_manager      = NULL;
    private $_goutteClient = NULL;

    private $wikiLinks = [
        Browser::BROWSER_CHROME   => "https://en.wikipedia.org/wiki/Google_Chrome",
        Browser::BROWSER_EXPLORER => 'https://en.wikipedia.org/wiki/Internet_Explorer',
        Browser::BROWSER_FIREFOX  => 'https://en.wikipedia.org/wiki/Firefox',
        Browser::BROWSER_OPERA    => 'https://en.wikipedia.org/wiki/Opera_(web_browser)',
        Browser::BROWSER_SAFARI   => 'https://en.wikipedia.org/wiki/Safari_(web_browser)'
    ];

    public function __construct(EntityManager $manager, Client $goutteClient)
    {
        $this->_manager      = $manager;
        $this->_goutteClient = $goutteClient;
    }

    private function collectBrowsersStableRelease()
    {
        $collectedStableReleaseList = [];

        foreach($this->wikiLinks as $browser => $wikiLink)
        {
            $crawler = $this->_goutteClient->request("GET", $wikiLink);

            $status_code = $this->_goutteClient->getResponse()->getStatus();

            if( $status_code != 200 )
                continue;

            $stableReleaseContent = $this->scrapStableRelease($crawler);

            if( !$stableReleaseContent )
                continue;

            $stableReleaseVersion = $this->cutStableReleaseVersion($stableReleaseContent);

            if( !$stableReleaseVersion )
                continue;

            $collectedStableReleaseList[$browser] = $stableReleaseVersion;
        }

        return $collectedStableReleaseList;
    }

    private function scrapStableRelease(Crawler $crawler)
    {
        $wikiBlocks = $crawler->filter('body > #content > #bodyContent > #mw-content-text > .infobox > tr');

        for($i = 1; $i <= $wikiBlocks->count(); $i++)
        {
            $blockExists = ($wikiBlocks->eq($i)->filter('th')->count()) && ($wikiBlocks->eq($i)->filter('th')->text() == "Stable release");

            if( $blockExists )
                return ( $wikiBlocks->eq($i)->filter('td')->text() ) ?: FALSE;
        }

        return FALSE;
    }

    private function cutStableReleaseVersion($stableReleaseContent)
    {
        $versionRegexPattern = "/([0-9]+\.)([0-9]\.?)+/";

        preg_match_all($versionRegexPattern, $stableReleaseContent, $matches, PREG_PATTERN_ORDER);

        return ( !empty($matches[0][0]) ) ? $matches[0][0] : FALSE;
    }

    public function updateBrowserStableRelease()
    {
        $collectedStableReleaseList = $this->collectBrowsersStableRelease();

        $browserNameList = array_keys($collectedStableReleaseList);

        $browserList = $this->_manager->getRepository('AppBundle:Meat\Browser')
            ->findByName($browserNameList);

        foreach($browserList as $browser) {
            var_dump( $browser->getBrowserVersion() );
        }
    }

    public function collectBrowsersMarketShare()
    {

    }
}