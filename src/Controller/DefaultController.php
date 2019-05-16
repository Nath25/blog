<?php
// src/Controller/DefaultController.php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
    * @Route("/",name="app_index")
    */
    public function index()
    {
        /*return new Response(
            '<html><body>Blog Index</body></html>'
        );*/
        return $this->render('blog/default.html.twig', [
            'owner' => 'nath',
    ]);
    }
}