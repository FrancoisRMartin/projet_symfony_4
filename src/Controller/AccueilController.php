<?php

namespace App\Controller;

use App\Repository\ChambreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="app_accueil")
     */
    public function index(ChambreRepository $repo): Response
    {
        $chambres = $repo->findBy([], ['dateEnregistrement' => 'DESC'], 3); // ou ASC pour le premier créé
        return $this->render('accueil/index.html.twig', [
            'accChambres' => $chambres
        ]);
    }
}
