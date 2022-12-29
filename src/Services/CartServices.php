<?php

namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class CartServices{

    private $sesssion;
    private $repoProduct;

    public function __construct(SessionInterface $session, ProductRepository $repoProduct)
    {
        $this->session = $session;
        $this->repoProduct = $repoProduct;
    }

    public function addToCart($id){
        $cart = $this->getCart();
        if(isset($cart[$id])){
            //Produit dèjà dans le panier on incremente
            $cart[$id]++;
        }else{
            //Le produit n'est pas encore dans le panier
            $cart[$id] = 1;
        }

        // après avoir recuperer le panier et ajouter un elment, il faut faire une mise à jour de la session
        $this->updateCart($cart);

    }

    public function deleteFromCart($id){
        //On recupère le panier dans un premier temps
        $cart = $this->getCart();

        //Est ce que le produit existe deja dans le panier?
        if(isset($cart[$id])){
            //On verifier si le produits existe plus d'une fois dans le panier?
            //On va de decrementer
            if($cart[$id] > 1){
                $cart[$id]--;
            }else{
                //Dans notre car il y a 1 seul produit dans le panier, on le supprime.
                unset($cart[$id]);
            }
        }

         // après avoir recuperer le panier et ajouter un elment, il faut faire une mise à jour de la session
         $this->updateCart($cart);
        
    }

    public function deleteAllToCart($id){

        //On recupère le panier dans un premier temps
        $cart = $this->getCart();

        //Est ce que le produit existe deja dans le panier? OUI
        //Dans ce car on supprime toute la quantité sans se poser de question.
            unset($cart[$id]);

         // après avoir recuperer le panier et ajouter un elment, il faut faire une mise à jour de la session
         $this->updateCart($cart);
        
    }

    public function deleteCart(){
        // Dans ce cas on vide le contenu du panier.
        $this->updateCart([]); 
    }

    public function updateCart($cart){
        $this->session->set('cart', $cart);
        $this->session->set('cartData', $this->getFullCart());
    }

    public function getCart(){
        return $this->session->get('cart', []);
    }

    public function getFullCart(){
        //On recupère le panier dans un premier temps
        $cart = $this->getCart();

        $fullCart = [];

        foreach ($cart as $id => $quantity) {
            $product = $this->repoProduct->find($id);
            # code...
            if($product){
                //produit recupéré avec succès
                $fullCart[] = [
                    "quantity" => $quantity,
                    "product" => $product
                ];
            }else{
                //id incorrecte
                $this->deleteFromCart($id);
            }
        }

        return $fullCart;

    }
}