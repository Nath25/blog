<?php


namespace App\Controller;

use App\Form\CategoryType;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class CategoryController extends AbstractController
{


     /**
     * @Route("/blog/categorylist/", name="category_list")
     * @param Request $request
     * @param ObjectManager $manager
     * @IsGranted("ROLE_ADMIN")
     * @return Response A response instance
     */
    public function add(Request $request, ObjectManager $manager)
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $manager->persist($category);
            $manager->flush();

            $this->addFlash('success', 'A new category has been created');

            return $this->redirectToRoute(
                'category_list'
            );
        }
        return $this->render(
            'blog/categorylist.html.twig', [
                'categories' => $categories,
                'form' => $form->createView()
            ]
        );
    }



}