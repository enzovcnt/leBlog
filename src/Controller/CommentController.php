<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    #[Route('/comment/{id}', name: 'app_comment')]
    public function index(Post $post, EntityManagerInterface $manager, Request $request): Response
    {
        if(!$this->getUser() || !$post)
        {
            return $this->redirectToRoute('app_login');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentForm::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setAuthor($this->getUser());
            $comment->setPost($post);
            $manager->persist($comment);
            $manager->flush();

        }
        return $this->redirectToRoute('app_post_show', [
            'id' => $post->getId(),
        ]);
    }

    #[Route('/comment/delete/{id}', name: 'app_comment_delete')]
    public function delete(Comment $comment, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser() || !$comment)
        {
            return $this->redirectToRoute('app_login');
        }

        $post = $comment->getPost();
        if($comment->getAuthor() == $this->getUser())
        {
            $manager->remove($comment);
            $manager->flush();
        }
        return $this->redirectToRoute('app_post_show', [
            'id' => $post->getId()
        ]);
    }

    #[Route('/comment/{id}/edit', name: 'app_comment_edit')]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser() || !$comment)
        {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(CommentForm::class, $comment);
        $form->handleRequest($request);
        $post = $comment->getPost();
        if($form->isSubmitted() && $form->isValid()){
            $comment->setAuthor($this->getUser());
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('app_post_show', [
                'id' => $post->getId()
            ]);
        }
        return $this->render('comment/index.html.twig',[
            'comment' => $comment,
            'formCommentEdit' => $form->createView(),
        ]);

    }
}
