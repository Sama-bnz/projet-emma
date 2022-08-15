<?php

namespace App\Controller\front;

use App\Repository\CategoryPrestationRepository;
use App\Repository\PrestationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontPrestationController extends AbstractController
{
    /**
     * @Route("/list/prestations", name="list_prestations")
     */
    public function listPrestation(CategoryPrestationRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();
        return $this->render('front/list_prestations.html.twig', [
            'categories' => $categories
        ]);
    }


    /**
     * @Route("/prestation/{id}", name="show_prestation")
     */
    public function showPrestation(PrestationRepository $prestationRepository,CategoryPrestationRepository $categoryRepository, $id)
    {
        $prestation = $prestationRepository->find($id);
        $categories = $categoryRepository->find($id);

        return $this->render('front/show_prestation.html.twig', [
            'prestation' => $prestation,
            'category' => $categories,
            'category_prestation' => $categories
        ]);
    }
}