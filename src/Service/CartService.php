<?php

namespace App\Service;

use App\Repository\ChambreRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $repo;
    private $rs;

    public function __construct(ChambreRepository $repo, RequestStack $rs)
    {
        $this->rs = $rs;
        $this->repo = $repo;
    }

    public function add($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id]))
            $cart[$id]++;
        else
            $cart[$id] = 1;

        $session->set('cart', $cart);
    }

    public function minus($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])){
            if($cart[$id] > 1) {
                $cart[$id]--;
            }
            else {
                unset($cart[$id]);
            }
        }
        // On sauvegarde la session
        $session->set('cart', $cart);
    }
    
    public function remove($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])){
                unset($cart[$id]);
        }
        // On sauvegarde la session
        $session->set('cart', $cart);
    }

    public function getCartWithData()
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);
        $qt = 0;

        $cartWithData = [];

        foreach($cart as $id => $quantity)
        {
            $cartWithData[] = [
                'produit' => $this->repo->find($id),
                'quantite' => $quantity
            ];
            $qt += $quantity;
        }
        $session->set('qt', $qt);
        return $cartWithData;
    }

    public function getTotal()
    {        
        $total = 0;

        foreach($this->getCartWithData() as $item)
        {
            $totalItem = $item['produit']->getPrix() * $item['quantite'];
            $total += $totalItem;
        }

        return $total;
    }
}