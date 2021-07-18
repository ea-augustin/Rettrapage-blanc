<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $articles = $this->articleRepository->findAll();

        return $this->render(
            'home/index.html.twig',
            [
                'articles' => $articles,
            ]
        );
    }

    #[Route('/article/{article}', name: 'detail',requirements: ['id' => '\d+'])]
    public function detail(
        Article $article
    ): Response {
        return $this->render(
            'home/detail.html.twig',
            [
               'article' => $article
            ]
        );
    }
}
