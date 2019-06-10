<?php


namespace App\Controller;


use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;


class BlogController extends AbstractController
{
    /**
     * Show all row from article's entity
     *
     * @Route("/blog", name="blog_index")
     * @return Response A response instance
     */
    public function index(Request $request): Response
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        if (!$articles) {
            throw $this->createNotFoundException(
                'No article found in article\'s table.'
            );
        }

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);



        return $this->render(
            'blog/index.html.twig',
            ['articles' => $articles,
            'form' => $form->createView()]
        );

    }


    /**
     * Getting a article with a formatted slug for title
     *
     * @param string $slug The slugger
     *
     * @Route("/blog/show/{slug<^[a-z0-9-]+$>}",
     *     defaults={"slug" = null},
     *     name="blog_show")
     * @return Response A response instance
     */
    public function show(string $slug = 'Article sans titre') : Response
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
                'No article with '.$slug.' title, found in article\'s table.'
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
     * @Route("/category/{name}", name="show_category")
    * @return Response
    */
    public function showByCategory(Category $category) : Response
    {

        return $this->render(
            'blog/category.html.twig',
            [
                'category' => $category
            ]);
    }



//    /**
////     * @Route("/category/{categoryName}", name="show_category")
////     *
////     * @return Response
////     */
//    public function showByCategory(string $categoryName) : Response
//    {
//
//        if (!$categoryName) {
//            throw $this
//                ->createNotFoundException('No category name has been set');
//        }
//
//        //$category = $categoryName->getCategory();
//        $category = $this->getDoctrine()
//            ->getRepository(Category::class)
//            ->findOneByName($categoryName);
//
//        /*$articles = $this->getDoctrine()
//            ->getRepository(Article::class)
//            ->findBy(['category' => $category]);*/
//
//        //$articles = $category->getArticles();
//
//        return $this->render(
//            'blog/category.html.twig',
//            [
//                'category' => $category
//        ]);
//    }

    /**
     * @Route ("/tag/{id}", name="category_tag")
     * @return Response
     */
    public function tag(Tag $tag)
    {
        return $this->render('blog/tag.html.twig',[
            'tag' => $tag
        ]);
    }

}
