<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\GoogleUserFactory;
use Doctrine\Persistence\ManagerRegistry;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class GoogleCheckController extends AbstractController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }
    /**
     * @Route("/google/check", name="app_check_google")
     */
    public function connect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google') // key used in knpu_oauth2_client.yaml
            ->redirect(['email']); // Ustawienie access_type na offline
    }

    /**
     * @Route("/google/connect", name="app_connect_google")
     */
    public function connectService(
        ClientRegistry $clientRegistry,
        GoogleUserFactory $userFactory,
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager
    )
    {
        $client = $clientRegistry->getClient('google');
        $accessToken = $client->getAccessToken();
        $userCredentials = $client->fetchUserFromToken($accessToken)->toArray();
        $userRepository = $this->managerRegistry->getRepository(User::class);
        $userCredentials['token'] = $accessToken->getToken();
        $user = $userRepository->findOneByEmailField($userCredentials['email']);
        if (!$user) {
            $user = $userFactory->create($userCredentials);
        }
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage->setToken($token);

        // Dokończ proces uwierzytelniania
        $authenticatedToken = $authenticationManager->authenticate($token);

        // Ustaw zautentykowany token
        $tokenStorage->setToken($authenticatedToken);
        return new RedirectResponse($this->generateUrl('index'));
    }
}
