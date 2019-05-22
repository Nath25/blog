<?php
// src/Controller/BlogController.php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use App\Form\ArticleSearchType;
use App\Form\CategoryType;


use PhpParser\Node\Expr\Cast\Object_;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;


class BlogController extends AbstractController
{
    /**
     * Show all row from article's entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        if (!$articles) {
            throw $this->createNotFoundException(
                'No article found in article\'s table.'
            );
        }

        $form = $this->createForm(
            ArticleSearchType::class,
            null,
            ['method' => Request::METHOD_GET]
        );



        return $this->render(
            'blog/index.html.twig', [
                'articles' => $articles,
                'form' => $form->createView(),
            ]
        );
    }

//* @Route("/{slug<^[a-z0-9-]+$>}",
    /**
     * Getting a article with a formatted slug for title
     *
     * @param string $slug The slugger
     *
     * @Route("/{slug}",
     *     defaults={"slug" = null},
     *     name="blog_show")
     * @return Response A response instance
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find an article in article\'s table.');
        }

        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article with ' . $slug . ' title, found in article\'s table.'
            );
        }

        return $this->render(
            'blog/show.html.twig',
            [
                'article' => $article,
                'slug' => $slug,
            ]
        );
    }





    /**
     * Getting a category with a formatted categoryName for name
     * @Route("/category/{name}", name="show_category")
     * * @return Response A response instance
     */
    public function showByCategory (category $category) : Response
    {

        $article = $category->getArticles();

        $category = new Category();

        return $this->render(
            'blog/category.html.twig',
            [
                'category' => $category,
                'articles' => $article
            ]
        );

    }



    /**
     * @Route("/blog/categorylist/{category_id}", name="show_oneCat")
     * @ParamConverter("id", class="App\Entity\Category", options={"id"="category_id"})
     */
    public function showCategories(Category $id) {
        return $this->render('blog/categoryList.html.twig', [
            'category'=> $id
        ]);
    }



    /**
     * @Route("/tag/{name}", name="show_tag")
     */
    public function showByTag (Tag $tag) : Response
    {

        $article = $tag->getArticles();


        return $this->render(
            'blog/tag.html.twig',
            [
                'tag' => $tag,
                'articles' => $article
            ]
        );

    }

}