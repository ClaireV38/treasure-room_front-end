<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/token/{targetPath}", name="app_token")
     * @param string $targetPath
     * @param HttpClientInterface $client
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getToken(string $targetPath, HttpClientInterface $client, EntityManagerInterface $entityManager)
    {

        $arrayAuthenticate = [
            'grant_type' => 'password',
            'client_id' => '93696e7e-e302-4ebe-b50f-e976e7a20f2a',
            'client_secret' => 'JVnptbFa3HrCygv1b7Lz9MqapfNGvR83DabSMDQM',
            'username' => 'su@gmail.com',
            'password' => '1234',
        ];
        $response = $client->request(
            'POST',
            'http://127.0.0.1:8000/oauth/token', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' =>
                    $arrayAuthenticate
            ]
        );
        $user = $this->getUser();
        $user->setApiToken($response->toArray()['access_token']);
        $entityManager->flush();
        if ('home' == $targetPath) {
            return $this->redirectToRoute('app_index');
        } else
            $targetPath = str_replace('*', '/', $targetPath);
        return new RedirectResponse($targetPath);
    }
}
