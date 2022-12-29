<?php

namespace App\Controller;

use App\Services\CartServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cartServices;

    public function __construct(CartServices $cartServices)
    {
        $this->cartServices = $cartServices;
    }

    /**
     * @Route("/cart", name="app_cart")
     */
    public function index(): Response
    {
        //On recupère le panier
        $cart = $this->cartServices->getFullCart();

        //Si le panier est vide , on redirige vers home
        if(!$cart){
            return $this->redirectToRoute("app_home");
        }
        
        return $this->render('cart/index.html.twig', [
            'cart' => $cart
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_app_add")
     */
    public function addToCart($id): Response
    {
        $this->cartServices->addToCart($id);

        return $this->redirectToRoute("app_cart");
    }

    /**
     * @Route("/cart/delete/{id}", name="app_delete_cart")
     */
    public function deleteFromCart($id): Response
    {
        $this->cartServices->deleteFromCart($id);

        return $this->redirectToRoute("app_cart");
    }

    /**
     * @Route("/cart/delete_all/{id}", name="app_delete_all_cart")
     */
    public function deleteAllToCart($id): Response
    {
        $this->cartServices->deleteAllToCart($id);

        return $this->redirectToRoute("app_cart");
    }
}
