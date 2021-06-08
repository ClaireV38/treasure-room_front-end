<?php

namespace App\Controller;

use App\Data\AssetData;
use App\Data\SearchData;
use App\Form\AssetType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/asset", name="asset_")
 */
class AssetController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param HttpClientInterface $client
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function index(
        HttpClientInterface $client,
        Request $request,
        UserRepository $userRepository): Response
    {
        $response = $client->request(
            'GET',
            'http://127.0.0.1:8000/api/v1/assets/'
        );
        $assets = $response->toArray()['data'];
        return $this->render('asset/index.html.twig', [
            'assets' => $assets,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @param Request $request
     * @param HttpClientInterface $client
     * @return Response
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function new(Request $request, HttpClientInterface $client): Response
    {
        $assetData = new AssetData();
        $form = $this->createForm(AssetType::class, $assetData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = $this->getUser()->getApiToken();
            $arrayAsset = (array)$assetData;

            $response = $client->request(
                'POST',
                'http://127.0.0.1:8000/api/v1/assets', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'json' => $arrayAsset
                ]
            );
            $this->addFlash('success', 'le trésor a bien été ajouté ');
            return $this->redirectToRoute('app_index');
        }
        return $this->render('asset/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param HttpClientInterface $client
     * @param int $id
     * @return Response
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @IsGranted("ROLE_USER")
     */
    public function show(HttpClientInterface $client, int $id): Response
    {
        $token = $this->getUser()->getApiToken();

        $response = $client->request(
            'GET',
            'http://127.0.0.1:8000/api/v1/assets/' . $id, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]]);
        $asset = $response->toArray()['data'][0];
        return $this->render('asset/show.html.twig', [
            'asset' => $asset,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, int $id): Response
    {
        $assetData = new AssetData();
        $form = $this->createForm(AssetType::class, $assetData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('adventurer_index');
        }

        return $this->render('asset/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public
    function delete(int $id, HttpClientInterface $client): Response
    {
        $token = $this->getUser()->getApiToken();

        $response = $client->request(
            'DELETE',
            'http://127.0.0.1:8000/api/v1/assets/' . $id, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]]);

        return $this->redirectToRoute('app_index');
    }
}
