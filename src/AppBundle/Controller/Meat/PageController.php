<?php
# AppBundle/Controller/Meat/PageController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Model\Meat\BrowserDetected;

class PageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $userError = NULL;

        $_detector = $this->get('detector');

        $browsers = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Meat\Browser')->findAll();

        if( !(($detectedBrowser = $_detector->getDetectedBrowser($browsers)) instanceof BrowserDetected) )
            $userError = $_detector->getUserError();

        return $this->render('AppBundle:Meat\Page:index.html.twig', [
            'userError'       => $userError,
            'detectedBrowser' => $detectedBrowser,
            'browsers'        => $browsers
        ]);
    }
}