<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $article, SessionInterface $session): Response
    {
        if (!$session->has('total')) {
            $session->set('total', 0); // if total doesn’t exist in session, it is initialized.
        }

        $total = $session->get('total'); // get actual value in session with ‘total' key.

        return $this->render('article/index.html.twig', [
            'articles' => $article->findAllWithCategoriesAndTags(),
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     * @IsGranted("ROLE_AUTHOR")
     */
    public function new(Request $request, Slugify $slugify, \Swift_Mailer $mailer): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();


            $article->setSlug($slugify->generate($article->getTitle()));
            $author = $this->getUser();
            $article->setAuthor($author);

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'The new article has been created');

            $messageContent = $this->renderView(
                'mail/mail.html.twig', [
                'article' => $article
            ]);

            $message = (new \Swift_Message("Un nouvel article vient d'être publié !"))
                ->setFrom($this->getParameter('mailer_from'))
                ->setTo($this->getParameter('mailer_from'))
                ->setBody($messageContent, 'text/html');

            $mailer->send($message);

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     * @IsGranted("ROLE_AUTHOR")
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_AUTHOR")
     */
    public function edit(Request $request, Article $article, Slugify $slugify): Response
    {

        if (($article->getAuthor()->getId() == $this->getUser()->getId()) | $this->isGranted(["ROLE_ADMIN"])) {
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);
        } else {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setSlug($slugify->generate($article->getTitle()));

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
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('danger', 'The article has been successfully deleted');
        }

        return $this->redirectToRoute('article_index');
    }
}
