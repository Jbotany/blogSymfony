<?php


namespace App\Controller;


use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;


class CategoryController extends AbstractController
{

    /**
     * @Route ("/add/category", name="category_add")
     * @return Response
     */
    public function add(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute('blog_index');
        }

        return $this->render('blog/add.html.twig', [
            'formCategory' => $form->createView()
        ]);
    }

}