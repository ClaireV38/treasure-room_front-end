<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdventurerController extends AbstractController
{
    /**
     * @Route("adventurer/", name="adventurer_index")
     */
    public function index(): Response
    {
        $adventurer = $this->getUser();
        return $this->render('adventurer/index.html.twig', [
        ]);
    }
}
