<?php

namespace App\Controller;

use App\Service\GoogleKeepApiService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(UserService $userService, GoogleKeepApiService $googleKeepApiService): Response
    {
        $notesList = [];
        $user = $userService->getCurrentUser();
        if ($user) {
            $notesList = $googleKeepApiService->getListOfNotes($user->getToken());
        }
        return $this->render('index/index.html.twig', [
            'notes' => $notesList
        ]);
    }
}