<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     * @param HttpClientInterface $client
     * @param Request $request
     * @return Response
     */
    public function index(HttpClientInterface $client, Request $request): Response
    {
        $response = $client->request(
            'GET',
            'http://127.0.0.1:8000/asset/'
        );
        $assets =$response->toArray();
        $lastAssets = array_slice($assets,-3,3);

        return $this->render('home/index.html.twig', [
            'assets' => $lastAssets
        ]);
    }
}
