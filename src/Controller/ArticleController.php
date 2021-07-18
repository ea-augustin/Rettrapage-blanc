<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/article')]
class ArticleController extends AbstractController
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    #[Route('/', name: 'article_index', methods: ['GET'])]
    public function index(
        ArticleRepository $articleRepository
    ): Response {
        return $this->render(
            'article/index.html.twig',
            [
                'articles' => $articleRepository->findAll(),
            ]
        );
    }

    #[Route('/new', name: 'article_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request
    ): Response {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $image = $form->get("image")->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dump($e);
                    die();
                }

                $article->setImage($newFilename);
            }


            $entityManager = $this->getDoctrine()->getManager();
            $article->setRelation($this->getUser());
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm(
            'article/new.html.twig',
            [
                'article' => $article,
                'form' => $form,
            ]
        );
    }

    /**
     * @param Article $article
     * @return Response
     */

    #[Route('/{id}', name: 'article_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(
        Article $article,

    ): Response {
        return $this->render(
            'article/show.html.twig',
            [
                'article' => $article,

            ]
        );
    }

    #[Route('/{id}/edit', name: 'article_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Article $article
    ): Response {
        if ($this->getUser()->hasRole("ROLE_ADMIN") or $article->getRelation() == $this->getUser()) {
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $article = $form->getData();
                $image = $form->get("image")->getData();
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $this->slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
                    try {
                        $image->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        dump($e);
                        die();
                    }
                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $article->setImage($newFilename);
                }

//            $this->getDoctrine()->getManager()->flush();
                $entityManager = $this->getDoctrine()->getManager();
                $article->setRelation($this->getUser());
                $entityManager->persist($article);
                $entityManager->flush();

                return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->renderForm(
                'article/edit.html.twig',
                [
                    'article' => $article,
                    'form' => $form,
                ]
            );
        } else {
            throw new UnauthorizedHttpException("Access Denied");
        }
    }

    #[Route('/{id}', name: 'article_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(
        Request $request,
        Article $article
    ): Response {
        if ($this->getUser()->hasRole("ROLE_ADMIN") or $article->getRelation() == $this->getUser()) {
            if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($article);
                $entityManager->flush();
            }

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        } else {
            throw new UnauthorizedHttpException("Access Denied");
        }
    }
}
