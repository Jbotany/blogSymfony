<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BlogController
 * @package App\Controller
 * @Route(name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="index")
     */
    public function index()
    {
        return $this->render('index.html.twig', [
            'owner' => 'Thomas',
        ]);
    }

    /**
     * @param $slug
     * @return Response
     * @Route("/blog/show/{slug}", name="show")
     */
    public function show($slug)
    {
        return $this->render('show.html.twig', [
            'slug' => $slug,
        ]);
    }
}
