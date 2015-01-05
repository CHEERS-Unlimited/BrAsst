<?php
# AppBundle/Controller/Meat/PageController.php
namespace AppBundle\Controller\Meat;

use AppBundle\Entity\Meat\Browser;
use AppBundle\Entity\Meat\BrowserVersion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $_detector = $this->get('detector');

        $browsers = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Meat\Browser')->findAll();

        if( !is_array($detectedDevice = $_detector->getDetectedDevice()) ) {
            $userError = $detectedDevice;
        } else {
            var_dump($detectedDevice);

            if( !(($clientBrowser = $_detector->getClientBrowser($browsers, $detectedDevice)) instanceof Browser) )
                $userError = $clientBrowser;

            if( !(($clientBrowserVersion = $_detector->getClientBrowserVersion($clientBrowser, $detectedDevice)) instanceof BrowserVersion) )
                $userError = $clientBrowserVersion;

            $isOutdated = $_detector->isClientOutdated($clientBrowserVersion, $detectedDevice);
        }

        return $this->render('AppBundle:Meat\Page:index.html.twig', [
            'detectedDevice'       => $detectedDevice,
            'clientBrowser'        => $clientBrowser,
            'clientBrowserVersion' => $clientBrowserVersion,
            'isOutdated'           => $isOutdated
        ]);
    }
}