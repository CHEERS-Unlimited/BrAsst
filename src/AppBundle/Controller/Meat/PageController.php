<?php
# AppBundle/Controller/Meat/PageController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Model\Meat\BrowserDetected;

class PageController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/",
     *      name="homepage",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "ua|en|ru", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/",
     *      name="homepage_default",
     *      defaults={"_locale" = "%locale%"}
     * )
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