<?php
# src/AppBundle/Controller/Meat/ApiController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\Serializer\Serializer,
    Symfony\Component\Serializer\Encoder\JsonEncoder,
    Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Service\Meat\Detector,
    AppBundle\Model\Meat\BrowserDetected;

class ApiController extends Controller
{
    const RESPONSE_TYPE_HTML = 'html';
    const RESPONSE_TYPE_JSON = 'json';

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
        if( ($httpOrigin = $request->server->get('HTTP_ORIGIN')) == NULL )
            throw $this->createNotFoundException();

        if ( ($httpUserAgent = $request->server->get('HTTP_USER_AGENT')) == NULL )
            return new Response('ERROR: Server index HTTP_USER_AGENT is empty', 500);

        $response_type = ( $request->query->has('type') ) ? $request->query->get('type') : self::RESPONSE_TYPE_HTML;

        $browsers = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Meat\Browser')->findAll();

        $userError = NULL;

        $_detector = $this->get('detector');

        $_detector->setDetectedBrowser($httpUserAgent, $browsers);

        if( !(($browserDetected = $_detector->getDetectedBrowser()) instanceof BrowserDetected) )
            $userError = $_detector->getUserError();

        switch ($response_type)
        {
            case self::RESPONSE_TYPE_HTML:
                $response = $this->responseHtml($userError, $browserDetected);
            break;

            case self::RESPONSE_TYPE_JSON:
                $response = $this->responseJson($userError, $browserDetected);
            break;

            default:
                $response = new Response('ERROR: Unknown response type', 500);
            break;
        }

        $response->headers->set("Access-Control-Allow-Origin", $httpOrigin);

        return $response;
    }

    private function responseHtml($userError, $browserDetected)
    {
        $isOutdated = ($browserDetected->getIsOutdated() === TRUE);
        $deprecated = ($browserDetected->getUserWarning() === Detector::USER_WARNING_UNSUPPORTED_OS);

        if( !$isOutdated && !$deprecated )
            return new Response(NULL, 200);

        return $this->render('AppBundle:Meat\Api:widget.html.twig', [
            'userError'       => $userError,
            'isOutdated'      => $isOutdated,
            'deprecated'      => $deprecated,
            'browserDetected' => $browserDetected
        ]);
    }

    private function responseJson($userError, $browserDetected)
    {
        $serializer = new Serializer(
            [new GetSetMethodNormalizer()],
            [new JsonEncoder()]
        );

        return ( $userError )
            ? new JsonResponse($serializer->serialize(['userError' => $userError], 'json'), 500)
            : new JsonResponse($serializer->serialize($browserDetected, 'json'), 200);
    }
}