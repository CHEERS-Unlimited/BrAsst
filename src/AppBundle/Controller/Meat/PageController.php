<?php
# AppBundle/Controller/Meat/PageController.php
namespace AppBundle\Controller\Meat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $bd = $this->get('browser_detection');

        var_dump(
            $bd->getBrowser(),
            $bd->getVersion()
        );

        return $this->render('AppBundle:Meat:index.html.twig');
    }
}