<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Asset;
use App\Form\AssetType;
use App\Form\ResetType;
use App\Form\SearchByCategoryFormType;
use App\Form\SearchByOwnerFormType;
use App\Form\SearchFormType;
use App\Repository\AssetRepository;
use App\Repository\CategoryRepository;
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
     * @param AssetRepository $assetRepository
     * @param CategoryRepository $categoryRepository
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
        AssetRepository $assetRepository,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository): Response
    {
        $response = $client->request(
            'GET',
            'http://127.0.0.1:8000/asset/'
        );
        $assets = $response->toArray();
        return $this->render('asset/index.html.twig', [
            'assets' => $assets,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $asset = new Asset();
        $form = $this->createForm(AssetType::class, $asset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($asset);
            $asset->setDepositDate(new \DateTime('now'));
            $asset->setOwner($this->getUser());
            $entityManager->flush();
            $this->addFlash('success', 'le trésor a bien été ajouté ');

            return $this->redirectToRoute('adventurer_index');
        }

        return $this->render('asset/new.html.twig', [
            'asset' => $asset,
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
        $response = $client->request(
            'GET',
            'http://127.0.0.1:8000/asset/' . $id,[
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer 1ec83b99edf7f1635690bfd4cb1315b31fbf071c87ff9565831081fac88375feb4074045d63ef2e52db73caa3b23bd01125dc3cdc613e86f88436eac'
        ]]);
        $asset = $response->toArray();
        return $this->render('asset/show.html.twig', [
            'asset' => $asset,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Asset $asset): Response
    {
        $form = $this->createForm(AssetType::class, $asset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('adventurer_index');
        }

        return $this->render('asset/edit.html.twig', [
            'asset' => $asset,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Asset $asset): Response
    {
        if ($this->isCsrfTokenValid('delete' . $asset->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($asset);
            $entityManager->flush();
        }

        return $this->redirectToRoute('adventurer_index');
    }
}
