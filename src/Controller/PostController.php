<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentForm;
use App\Form\PostForm;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    #[Route('/', name: 'app_posts')]
    public function index(PostRepository $postRepository): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new')]
    public function create(EntityManagerInterface $manager, Request $request): Response
    {

        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $post = new Post();
        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $post->setAuthor($this->getUser());
            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/create.html.twig', [
            'formNew' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}', name: 'app_post_show', priority: -1)]
    public function show(Post $post): Response
    {
        if(!$this->getUser() || !$post)
        {
            return $this->redirectToRoute('app_login');
        }
        $comment = new Comment();
        $form = $this->createForm(CommentForm::class, $comment);
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'formComment' => $form->createView(),
            'comment' => $comment,
        ]);

    }

    #[Route('/post/{id}/edit', name: 'app_post_edit')]
    public function edit(Post $post, Request $request, EntityManagerInterface $manager): Response
    {

        if(!$this->getUser() || !$post)
        {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }
        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'formEdit' => $form->createView()
        ]);
    }

    #[Route('/post/{id}/delete', name: 'app_post_delete')]
    public function delete(Post $post, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser() || !$post)
        {
            return $this->redirectToRoute('app_login');
        }
        $manager->remove($post);
        $manager->flush();
        return $this->redirectToRoute('app_posts');
    }
}
