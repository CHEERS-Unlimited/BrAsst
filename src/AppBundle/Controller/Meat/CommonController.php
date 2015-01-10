<?php
# src/AppBundle/Controller/Meat/CommonController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Model\Meat\BrowserDetected;

class CommonController extends Controller
{
    public function widgetHtmlAction()
    {
        $userError = NULL;

        $_detector = $this->get('detector');

        $browsers = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Meat\Browser')->findAll();

        if( !(($detectedBrowser = $_detector->getDetectedBrowser($browsers)) instanceof BrowserDetected) )
            $userError = $_detector->getUserError();

        return $this->render('AppBundle:Meat\Common:widget.html.twig', [
            'userError'       => $userError,
            'detectedBrowser' => $detectedBrowser,
            'browsers'        => $browsers
        ]);
    }
}