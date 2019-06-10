<?php


namespace App\Controller;


use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * Class CategoryController
 * @package App\Controller
 */
class CategoryController extends AbstractController
{

    /**
     * @Route ("/category/new", name="category_add")
     * @return Response
     * @IsGranted("ROLE_ADMIN")
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