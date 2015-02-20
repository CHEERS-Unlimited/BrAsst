<?php
# AppBundle/Controller/Meat/StateController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Model\Meat\BrowserDetected;

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
     *      name="homepage_default",
     *      defaults={"_locale" = "%locale%"}
     * )
     */
    public function indexAction()
    {
        $browsers = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Meat\Browser')->findAll();

        return $this->render('AppBundle:Meat:index.html.twig', [
            'browsers' => $browsers
        ]);
    }

    public function homeAction(Request $request, $browsers)
    {
        if ( ($httpUserAgent = $request->server->get('HTTP_USER_AGENT')) == NULL ) {
            return new Response('ERROR: Server index HTTP_USER_AGENT is empty', 500);
        }

        $userError = NULL;

        $_detector = $this->get('detector');

        if( !(($browserDetected = $_detector->getDetectedBrowser($httpUserAgent, $browsers)) instanceof BrowserDetected) )
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