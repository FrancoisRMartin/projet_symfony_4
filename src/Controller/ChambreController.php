<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeFormType;
use App\Repository\ChambreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ChambreController extends AbstractController
{
    /**
     * @Route("/chambres", name="app_chambres")
     */
    public function index(ChambreRepository $repo): Response
    {
        $chambres = $repo->findAll();

        return $this->render('chambres/index.html.twig', [
            'chambres' => $chambres
        ]);
    }

    /**
     * @Route("/chambre/{id}", name="show_chambre")
     */
    public function show($id, ChambreRepository $repo, Request $superGlobals, EntityManagerInterface $manager): Response
    {
        $commande = new Commande();

        // CREATEFORM permet de récupérer un formulaire existant #}
        $form = $this->createForm(CommandeFormType::class, $commande);

        // HandleRequest permet d'insérer les données du formulaire dans l'objet $article
        //Elle permet aussi de faire des vérifications sur le formulaire
        $form->handleRequest($superGlobals);

        // Appel de la méthode FIND pour récupérer l'éléments ID
        $chambre = $repo->find($id);
        $messageForm = "La commande a été ajouté !";

        if ($form->isSubmitted() && $form->isValid()) {

            $commande->setDateEnregistrement(new \DateTime())
                ->setChambre($chambre)
                ->setMembre($this->getUser());
            
            $manager->persist($commande);
            $manager->flush();
            $this->addFlash('success', $messageForm);
            return $this->redirectToRoute('show_chambre', [
                'id' => $chambre->getId()
            ]);
        }
        //dd($chambre);
        return $this->render('chambres/chambre.html.twig', [
            'formCommande' => $form->createView(),
            'chambre' => $chambre
        ]);
    }
}
