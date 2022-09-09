<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Service\CartService;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="app_cart")
     */
    public function index(CartService $cs): Response
    {
        return $this->render('panier/index.html.twig', [
            'items' => $cs->getCartWithData(),
            'total' => $cs->getTotal()
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add($id, CartService $cs)
    {
        $cs->add($id);
        return $this->redirectToRoute('app_cart');
    }
    
    /**
     * @Route("/cart/minus/{id}", name="cart_minus")
     */
    public function minus($id, CartService $cs)
    {   
        $cs->minus($id);
        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove($id, CartService $cs)
    {
        $cs->remove($id);
        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/deleteAll", name="cart_delete_all")
     */
    public function deleteAll(RequestStack $rs)
    {
        $session = $rs->getSession();

        $session->remove("cart");
        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/validate", name="cart_validate")
     */
    public function validate(CommandeRepository $repo, EntityManagerInterface $manager, RequestStack $rs, CartService $cs)
    {

        $cart = $cs->getCartWithData();
        $commande = new Commande;
        
            $commande->setMembre($this->getUser())
                    ->setChambre($cart[0]["produit"]);
                    // ->setQuantite($cart[0]["quantite"])
                    // ->setMontant($cs->getTotal())
                    // ->setEtat("En traitement")
                    // ->setDateEnregistrement(new \DateTime);               
            $manager->persist($commande);

            $produit = $cart[0]["produit"];
            $produit->setStock($produit->getStock() - $cart[0]["quantite"]);
            $manager->persist($produit);
        
        $manager->flush();

        $session = $rs->getSession();
        $session->remove("cart");

        $commandes = $repo->findBy(["membre" => $this->getUser()], ["dateEnregistrement" => "DESC"]);
        // je passe à find() l'id dans ma route pour récupérer l'article correspondant en BDD
        return $this->redirectToRoute('app_profil', [
            'tabCommandes' => $commandes
        ]);

    }
}
