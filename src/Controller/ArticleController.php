<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\User;
use App\Service\Slugify;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        $user = $this->getUser();


        //return $this->render('article/index.html.twig', [
        //  'articles' => $articleRepository->findAll(),
        //'user'=>$user,
        //]);


        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAllWithCategoriesAndTags(),
            'user'=>$user,
        ]);
    }

    /**
     * @IsGranted("ROLE_AUTHOR")
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request,Slugify $slugify ,\Swift_Mailer $mailer): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($article->getTitle());
            $article->setSlug($slug);
            $author = $this->getUser();
            $article->setAuthor($author);
            $entityManager->persist($article);
            $entityManager->flush();

           
            $message = (new \Swift_Message('Un nouvel article vient d\'être publié !'))
               // ->setFrom('cn@aol.com')
                //->setTo($adrDest)
               // ->setBody('Un nouvel article vient d\'être publié sur le blog ! '.$article->getTitle());

                ->setBody($this->renderView('article/email/notification.html.twig',
                                ['article' => $article]),'text/html');

            $mailer->send($message);

             // Once the form is submitted, valid and the data inserted in database, you can define the success flash message
            $this->addFlash('success', 'The new article has been created');
            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        $user = $this->getUser();
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'user'=> $user,
            'isFavorite' => $this->getUser()->isFavorite($article)
        ]);

    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article, Slugify $slugify): Response
    {
        $user = $this->getUser();
        $author = $article->getAuthor();
        if ($user != $author && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Accès refusé ! Vous n'avez pas les droits" );
        }
        //$this->denyAccessUnlessGranted('edit', $article);
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($article->getTitle());
            $article->setSlug($slug);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index', [
                'id' => $article->getId(),
            ]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('danger', 'The article has been deleted');
        }

        return $this->redirectToRoute('article_index');
    }



    /**
     * @Route("/{id}/favorite", name="article_favorite", methods={"GET","POST"})
     */
    public function favorite(Request $request, Article $article)
    {
        $user = $this->getUser();

        if (!$user->getArticleFavori()->contains($article)) {
            $user->addArticleFavori($article);

        }else {
            $user->removeArticleFavori($article);

        }
        $this->getDoctrine()->getManager()->flush();

        //return $this->render('article/show.html.twig', [
        //    'article' => $article,
        //    'user'=> $user,
        //]);

        return $this->json([
            'isFavorite' => $this->getUser()->isFavorite($article)
        ]);
    }
}


