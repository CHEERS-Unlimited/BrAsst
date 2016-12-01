<?php
# AppBundle/Controller/Meat/StateController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Meat\Browser,
    AppBundle\Model\Meat\BrowserDetected;

class StateController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/",
     *      name="index",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en|ru", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/",
     *      name="index_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function indexAction(Request $request)
    {
        if ( ($httpUserAgent = $request->server->get('HTTP_USER_AGENT')) == NULL )
            return new Response('ERROR: Server index HTTP_USER_AGENT is empty', 500);

        $browsers = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Meat\Browser')->findAll();

        $_detector = $this->get('detector');

        $_detector->setDetectedBrowser($httpUserAgent, $browsers);

        $browserDetected = $_detector->getDetectedBrowser();

        if( $browserDetected instanceof BrowserDetected &&
            $_detector->banishLesserIE($browserDetected->getBrowser(), $browserDetected->getClientVersion()) )
            return $this->forward('AppBundle:Meat\State:banishLesserIE', [
                'browserDetected' => $browserDetected
            ]);

        return $this->render('AppBundle:Meat:index.html.twig', [
            'browsers' => $browsers
        ]);
    }

    public function banishLesserIEAction($browserDetected)
    {
        $ieLink = Browser::LINK_EXPLORER;

        return $this->render('AppBundle:Meat:banishLesserIE.html.twig', [
            'browserDetected' => $browserDetected,
            'ieLink'          => $ieLink
        ]);
    }

    public function homeAction()
    {
        $_detector = $this->get('detector');

        $userError = NULL;

        if( !(($browserDetected = $_detector->getDetectedBrowser()) instanceof BrowserDetected) )
            $userError = $_detector->getUserError();

        return $this->render('AppBundle:Meat\State:home.html.twig', [
            'userError'       => $userError,
            'browserDetected' => $browserDetected
        ]);
    }

    public function aboutBrasstAction()
    {
        return $this->render('AppBundle:Meat\State:about_brasst.html.twig');
    }

    public function browsersAction($browsers)
    {
        return $this->render('AppBundle:Meat\State:browsers.html.twig', [
            'browsers' => $browsers
        ]);
    }

    public function brasstApiAction()
    {
        return $this->render('AppBundle:Meat\State:brasst_api.html.twig');
    }
}