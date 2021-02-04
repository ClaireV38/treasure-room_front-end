<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Form\AssetType;
use App\Form\ResetType;
use App\Form\SearchByCategoryFormType;
use App\Form\SearchByOwnerFormType;
use App\Repository\AssetRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/asset")
 */
class AssetController extends AbstractController
{
    /**
     * @Route("/", name="asset_index", methods={"GET","POST"})
     * @param Request $request
     * @param AssetRepository $assetRepository
     * @param CategoryRepository $categoryRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(
        Request $request,
        AssetRepository $assetRepository,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository): Response
    {
        $assets = $assetRepository->findall();

        $resetForm = $this->createForm(ResetType::class);
        $resetForm->handleRequest($request);

        $searchCategoryForm = $this->createForm(SearchByCategoryFormType::class);
        $searchCategoryForm->handleRequest($request);

        $searchOwnerForm = $this->createForm(SearchByOwnerFormType::class);
        $searchOwnerForm->handleRequest($request);


        if ($searchOwnerForm->isSubmitted() && $searchOwnerForm->isValid()) {
            var_dump('coucou');
            $owner = $searchOwnerForm->getData()['owner'];
            $assets = $assetRepository->findBy(['owner' => $owner]);
        }

        if ($searchCategoryForm->isSubmitted() && $searchCategoryForm->isValid()) {
            $category = $searchCategoryForm->getData()['category'];
            $assets = $assetRepository->findBy(['category' => $category]);
        }

        if ($resetForm->isSubmitted() && $resetForm->isValid()) {
            $assets = $assetRepository->findall();
        }

        return $this->render('asset/index.html.twig', [
            'assets' => $assets,
             'searchCategoryForm' => $searchCategoryForm->createView(),
             'searchOwnerForm' => $searchOwnerForm->createView(),
             'resetForm' => $resetForm->createView(),
        ]);
    }

    /**
     * @Route("/new", name="asset_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $asset = new Asset();
        $form = $this->createForm(AssetType::class, $asset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($asset);
            $asset->setOwner($this->getUser());
            $entityManager->flush();

            return $this->redirectToRoute('adventurer_index');
        }

        return $this->render('asset/new.html.twig', [
            'asset' => $asset,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/vote", name="asset_vote", methods={"GET"})
     */
    public function voteFor(Asset $asset, EntityManagerInterface  $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()->getVotes()->contains($asset)) {
            $this->getUser()->removeVote($asset);
        }
        else {
            $this->getUser()->addVote($asset);
        }
        $entityManager->flush();
        return $this->json([
            'hasVotedFor' => $this->getUser()->hasVotedFor($asset)
        ]);
    }

    /**
     * @Route("/{id}", name="asset_show", methods={"GET"})
     */
    public function show(Asset $asset): Response
    {
        return $this->render('asset/show.html.twig', [
            'asset' => $asset,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="asset_edit", methods={"GET","POST"})
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
     * @Route("/{id}", name="asset_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Asset $asset): Response
    {
        if ($this->isCsrfTokenValid('delete'.$asset->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($asset);
            $entityManager->flush();
        }

        return $this->redirectToRoute('asset_index');
    }
}
