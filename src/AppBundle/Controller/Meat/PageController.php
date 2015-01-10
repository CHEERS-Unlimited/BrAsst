<?php
# AppBundle/Controller/Meat/PageController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PageController extends Controller
{
    const RESPONSE_TYPE_HTML = 'html';
    const RESPONSE_TYPE_JSON = 'json';

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
     *      "/api/{_response_type}/{_locale}",
     *      name="api",
     *      defaults={"_response_type" = "html", "_locale" = "%locale%"},
     *      requirements={"_response_type" = "html|json", "_locale" = "%locale%|en|ru"}
     * )
     */
    public function apiAction(Request $request, $_response_type)
    {
        if( ($httpOrigin = $request->server->get('HTTP_ORIGIN')) == NULL ) {
            throw $this->createNotFoundException();
        } else {
            $response = new Response;

            $response->headers->set("Access-Control-Allow-Origin", $httpOrigin);

            if( ($httpUserAgent = $request->server->get('HTTP_USER_AGENT')) == NULL ) {
                return $response->setContent('ERROR #1: Server index HTTP_USER_AGENT is empty', 500);
            }

            switch($_response_type)
            {
                case self::RESPONSE_TYPE_HTML:
                    //get html
                    $response->setContent($this->forward('AppBundle:Meat\Common:widgetHtml')->getContent());
                break;

                case self::RESPONSE_TYPE_JSON:
                    //get json
                break;
            }

            return $response;
        }
    }
}