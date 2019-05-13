<?php
// src/Controller/BlogController.php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;


class BlogController extends AbstractController
{
    /**
    * @Route("/blog/index")
    */
    public function index()
    {
        /*return new Response(
            '<html><body>Blog Index</body></html>'
        );*/
        return $this->render('blog/index.html.twig', [
            'owner' => 'Thomas',
    ]);
    }
}
