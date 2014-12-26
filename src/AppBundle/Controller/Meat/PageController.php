<?php
# AppBundle/Controller/Meat/PageController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\OperatingSystem;
use DeviceDetector\Parser\Device\DeviceParserAbstract;
use DeviceDetector\Cache\CacheFile;

use Goutte\Client;

class PageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $this->get('collector')->updateBrowserStableRelease();
        $this->get('collector')->updateBrowsersMarketShare();

        return $this->render('AppBundle:Meat:index.html.twig');

        /*DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

        $dd = new DeviceDetector($this->get('request')->headers->get('User-Agent') );

        // OPTIONAL: Set caching method
        // By default static cache is used, which works best within one php process
        // To cache across requests use caching in files or memcache
        $dd->setCache(new CacheFile('./tmp/'));

        // OPTIONAL: If called, getBot() will only return true if a bot was detected  (speeds up detection a bit)
        $dd->discardBotInformation();

        $dd->parse();

        if ($dd->isBot()) {
            // handle bots,spiders,crawlers,...
            $botInfo = $dd->getBot();
        } else {
            $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
            $osInfo = $dd->getOs();
            $device = $dd->getDevice();
            $brand = $dd->getBrand();
            $model = $dd->getModel();

            var_dump( OperatingSystem::getOsFamily($osInfo['short_name']) );

            var_dump(
                $clientInfo, $osInfo, $device, $brand, $model
            );
        }

        $client = new Client();
        $crawler = $client->request('GET', "https://en.wikipedia.org/wiki/Google_Chrome");
        //$crawler = $client->request('GET', "https://en.wikipedia.org/wiki/Firefox");
        //$crawler = $client->request('GET', "https://en.wikipedia.org/wiki/Opera_(web_browser)");
        //$crawler = $client->request('GET', "https://en.wikipedia.org/wiki/Internet_Explorer");
        //$crawler = $client->request('GET', "https://en.wikipedia.org/wiki/Safari_(web_browser)");
        $status_code = $client->getResponse()->getStatus();

        if($status_code==200)
        {
            //process the documents
            $tableBlock = $crawler->filter('body > div#content > div#bodyContent > div#mw-content-text > table.infobox > tr');

            $siblingsNumber = count($tableBlock->siblings());

            for($i = 1; $i <= $siblingsNumber; $i++)
            {
                if( count($tableBlock->eq($i)->filter('th')) && $tableBlock->eq($i)->filter('th')->text() == 'Stable release' ) {
                    $stableRelease = $tableBlock->eq($i)->filter('td')->text();
                    break;
                }
            }
        }

        preg_match_all("/([0-9]+\.)([0-9]\.?)+/", $stableRelease, $matches, PREG_PATTERN_ORDER);

        var_dump($stableRelease, $matches[0][0]);

        if( empty($matches[0][0]) ) {
            return FALSE;
        }

        $latestStableVersion  = explode('.', $matches[0][0]);
        $currentClientVersion = explode('.', $clientInfo['version']);

        var_dump(
            $latestStableVersion, $currentClientVersion
        );

        foreach($latestStableVersion as $position => $subVersion)
        {
            if( isset($currentClientVersion[$position]) )
            {
                if( $currentClientVersion[$position] < $subVersion ) {
                    $isLatest = FALSE;
                    break;
                } else {
                    $isLatest = TRUE;
                }
            }
        }

        var_dump( $isLatest );

        if( $isLatest ) {
            print_r("Looks like you've got the latest {$clientInfo['name']} browser for {$osInfo['name']}");
        } else {
            print_r("Your {$clientInfo['name']} browser for {$osInfo['name']} is outdated. We recommend to download latest version:");

            var_dump(
                "https://www.google.com/chrome/browser/desktop",
                "https://www.mozilla.org/ru/firefox/new",
                "http://windows.microsoft.com/en-us/internet-explorer/download-ie",
                "http://support.apple.com/downloads/#safari",
                "http://www.opera.com/ru/computer/windows"
            );
        }

        $client = new Client();
        $link = "http://www.w3counter.com/globalstats.php";
        $crawler = $client->request('GET', $link);
        $status_code = $client->getResponse()->getStatus();

        if( $status_code == 200 )
        {
            $browsers = $crawler->filter('body > #wrap > div')->eq(2)->filter('.container > .row > .col-md-9 > .bargraphs > div > .bar');

            $browsersNumber = $browsers->count();

            for($i = 0; $i < $browsersNumber; $i++) {
                $statsBrowsers[] = [
                    'name'    => $browsers->eq($i)->filter('.lab')->text(),
                    'percent' => $browsers->eq($i)->filter('.value')->text()
                ];
            }

            var_dump($statsBrowsers);
        }*/
    }
}