<?php

namespace App\Controller;

use App\Form\CheckoutType;
use App\Services\CartServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout", name="app_checkout")
     */
    public function index(CartServices $cartServices, Request $request): Response
    {
         //On recupère l'utilisateur
         $user = $this->getUser();

         //On recupère le panier
         $cart = $cartServices->getFullCart();

         //si le panier est vide, on le redirige vers la page d'accueil
         if(!$cart){
            return $this->redirectToRoute("app_home");
         }

         //Si l'utilisateur n'a pas d'adresse on le redirige vers la page d'ajout d'adresse.
         if (!$user->getAddresses()->getValues()) {
            //on ajoute un message flash avant de rediriger vers la page d'ajut d'adresse
            $this->addFlash('checkout_message', 'please add an address to your account without continuing');
            return $this->redirectToRoute("app_address_new");
         }

         //Nous allons initialiser le formulaire
         $form = $this->createForm(CheckoutType::class,null,['user'=>$user]);

         // On va annalyser la requeteet ajoutant Request dans la fuction index
         $form->handleRequest($request);

         //traitement du formulaire

        return $this->render('checkout/index.html.twig',[

            'cart' => $cart,
            'checkout' => $form->createView()

        ]);
    }
}
