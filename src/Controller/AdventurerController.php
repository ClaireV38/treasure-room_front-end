<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdventurerController extends AbstractController
{
    /**
     * @Route("adventurer/", name="adventurer_index")
     * @param HttpClientInterface $client
     * @return Response
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function index(HttpClientInterface $client): Response
    {
        $adventurer = $this->getUser();

        $response = $client->request(
            'GET',
            'http://127.0.0.1:8000/api/v1/adventurers/' . $adventurer->getId()
        );
        $adventurerAssets = $response->toArray()['data'];

        return $this->render('adventurer/index.html.twig', [
            'assets' => $adventurerAssets
        ]);
    }
}
