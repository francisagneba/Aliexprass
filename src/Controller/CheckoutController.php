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
    private $cartServices;

    public function __construct(CartServices $cartServices)
    {
        $this->cartServices = $cartServices;
    }
    /**
     * @Route("/checkout", name="app_checkout")
     */
    public function index( Request $request): Response
    {
         //On recupère l'utilisateur
         $user = $this->getUser();

         //On recupère le panier
         $cart = $this->cartServices->getFullCart();

         //si ce n'est pas definit alors le panier est vide, on le redirige vers la page d'accueil
         if(!isset($cart['products'])){
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

         // On va annalyser la requete an ajoutant Request dans la fuction index
         $form->handleRequest($request);

        return $this->render('checkout/index.html.twig',[

            'cart' => $cart,
            'checkout' => $form->createView()

        ]);
    }
    
    //traitement du formulaire 

    /**
     * @Route("/checkout/confirm", name="app_checkout_confirm")
     */
    public function confirm(Request $request): Response {
        //On recupère l'utilisateur
        $user = $this->getUser();

        //On recupère le panier
        $cart = $this->cartServices->getFullCart();

         //si ce n'est pas definit alors le panier est vide, on le redirige vers la page d'accueil
         if(!isset($cart['products'])){
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

         // On va annalyser la requete an ajoutant Request dans la fuction index
         $form->handleRequest($request);

         if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();

            $address = $data['address'];
            $carrier = $data['carrier'];
            $informations = $data['informations'];

            return $this->render('checkout/confirm.html.twig',[

                'cart' => $cart,
                'address' => $address,
                'carrier' => $carrier,
                'informations' => $informations, 
                'checkout' => $form->createView()
    
            ]);

         }

         return $this->redirectToRoute("app_checkout");

    }
}
