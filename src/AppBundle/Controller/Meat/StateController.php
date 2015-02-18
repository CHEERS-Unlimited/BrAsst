<?php
# AppBundle/Controller/Meat/StateController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class StateController extends Controller
{
    protected $browsers = [];

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
        $this->browsers = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Meat\Browser')->findAll();

        return $this->render('AppBundle:Meat:index.html.twig');
    }

    public function homeAction()
    {
        return $this->render('AppBundle:Meat\State:home.html.twig');
    }

    public function aboutBrasstAction()
    {
        return $this->render('AppBundle:Meat\State:about_brasst.html.twig');
    }

    public function browsersAction()
    {
        return $this->render('AppBundle:Meat\State:browsers.html.twig');
    }

    public function brasstApiAction()
    {
        return $this->render('AppBundle:Meat\State:brasst_api.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/api/{_locale}",
     *      name="api",
     *      defaults={"_locale" = "%locale%"},
     *      requirements={"_locale" = "%locale%|en|ru"}
     * )
     */
    public function apiAction(Request $request)
    {
        if( ($httpOrigin = $request->server->get('HTTP_ORIGIN')) == NULL ) {
            throw $this->createNotFoundException();
        } else {
            $response_type = $request->query->get('type');

            $browsers = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Meat\Browser')->findAll();

            $response = $this->forward('AppBundle:Meat\Api:widget', [
                'response_type' => $response_type,
                'browsers'      => $browsers
            ]);

            $response->headers->set("Access-Control-Allow-Origin", $httpOrigin);

            return $response;
        }
    }
}