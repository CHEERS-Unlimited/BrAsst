<?php
# AppBundle/Controller/Meat/PageController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;
use DeviceDetector\Cache\CacheFile;

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

        return $this->render('AppBundle:Meat:index.html.twig');
    }
}