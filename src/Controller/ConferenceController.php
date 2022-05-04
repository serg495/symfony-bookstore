<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    #[Route('/conference', name: 'conference')]
    #[Route('/', name: 'homepage')]
    public function index(ConferenceRepository $repository): Response
    {
        $conferences = $repository->findAll();

        return new Response($this->twig->render('conference/index.html.twig', ['conferences' => $conferences]));
    }

    #[Route('/conference/{id}', name: 'conference')]
    public function show(Request $request, Conference $conference, CommentRepository $repository)
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $comments = $repository->getCommentPaginator($conference, $offset);

        return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $comments,
            'previous' => $offset - CommentRepository::PER_PAGE,
            'next' => min(count($comments), $offset + CommentRepository::PER_PAGE),
        ]));
    }
}
