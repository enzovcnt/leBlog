<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryForm;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $repository, Request $request, EntityManagerInterface $manager): Response
    {

        $material = new Category();
        $formMat = $this->createForm(CategoryForm::class, $material);
        $formMat->handleRequest($request);
        if ($formMat->isSubmitted() && $formMat->isValid()) {
            $manager->persist($material);
            $manager->flush();
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/index.html.twig', [
            'category' => $repository->findAll(),
            'formCategory' => $formMat->createView(),
        ]);
    }
}
