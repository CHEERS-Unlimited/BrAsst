<?php
# AppBundle/Controller/Meat/PageController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use DeviceDetector\DeviceDetector;
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
        DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

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
        if($status_code==200){
            //process the documents
            $tableBlock = $crawler->filter('body > div#content > div#bodyContent > div#mw-content-text > table.infobox > tr');

            //var_dump( $crawler->filter('body > div#content > div#bodyContent > div#mw-content-text > table.infobox > tr')->eq(3)->text() );
            //var_dump( $crawler->filter('body > div#content > div#bodyContent > div#mw-content-text > table.infobox > tr')->eq(4)->text() );

            $siblingsNumber = count($tableBlock->siblings());

            for($i = 1; $i < $siblingsNumber; $i++)
            {
                if( count($tableBlock->eq($i)->filter('th')) && $tableBlock->eq($i)->filter('th')->text() == 'Stable release' ) {
                    $stableRelease = $tableBlock->eq($i)->filter('td')->text();
                    break;
                }
            }
        }

        preg_match_all("/([0-9]+\.)([0-9]\.?)+/", $stableRelease, $matches, PREG_PATTERN_ORDER);

        var_dump($stableRelease, $matches[0][0]);

        return $this->render('AppBundle:Meat:index.html.twig');
    }
}