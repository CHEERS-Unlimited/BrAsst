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

use AppBundle\Model\Meat\BrowserDetected;

class ApiController extends Controller
{
    const RESPONSE_TYPE_HTML = 'html';
    const RESPONSE_TYPE_JSON = 'json';

    public function widgetAction(Request $request, $browsers, $response_type = self::RESPONSE_TYPE_HTML)
    {
        if ( ($httpUserAgent = $request->server->get('HTTP_USER_AGENT')) == NULL ) {
            return new Response('ERROR: Server index HTTP_USER_AGENT is empty', 500);
        }

        $userError = NULL;

        $_detector = $this->get('detector');

        if( !(($browserDetected = $_detector->getDetectedBrowser($httpUserAgent, $browsers)) instanceof BrowserDetected) )
            $userError = $_detector->getUserError();

        switch ($response_type)
        {
            case self::RESPONSE_TYPE_HTML:
                return $this->responseHtml($userError, $browserDetected);
            break;

            case self::RESPONSE_TYPE_JSON:
                return $this->responseJson($userError, $browserDetected);
            break;

            default:
                return new Response('ERROR: Unknown response type', 500);
            break;
        }
    }

    private function responseHtml($userError, $browserDetected)
    {
        return $this->render('AppBundle:Meat\Api:widget.html.twig', [
            'userError'       => $userError,
            'detectedBrowser' => $browserDetected
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