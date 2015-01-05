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
        //$this->get('collector')->updateBrowserStableRelease();
        //$this->get('collector')->updateBrowsersMarketShare();

        var_dump(
            $this->get('detector')->getDetectedDevice()
        );

        return $this->render('AppBundle:Meat:index.html.twig');
    }
}