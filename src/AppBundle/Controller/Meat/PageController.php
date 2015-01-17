<?php
# AppBundle/Controller/Meat/PageController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PageController extends Controller
{
    const TEST = 'en';

    /**
     * @Method({"GET"})
     * @Route(
     *      "/",
     *      name="homepage",
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

        return $this->render('AppBundle:Meat\Page:index.html.twig', [
            'browsers' => $browsers
        ]);
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