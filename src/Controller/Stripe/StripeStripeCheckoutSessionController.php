<?php

namespace App\Controller\Stripe;

use Stripe\Stripe;
use App\Entity\Cart;
use Stripe\Checkout\Session;
use App\Services\CartServices;
use App\Services\OrderServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeStripeCheckoutSessionController extends AbstractController
{
    /**
     * @Route("/create-checkout-session/{reference}", name="create_checkout_session")
     */
    public function index(?Cart $cart, OrderServices $orderServices, EntityManagerInterface $manager): JsonResponse
    {
        //on recupère tout le contenue du panier
        //$cart = $cartService->getFullCart();
        
        //On recupère ici l'utilisateur 
        $user = $this->getUser();

        if (!$cart) {
           return $this->redirectToRoute('app_home');
        }

        //Si lutilisateur est déjà venu ici , on enregistre sa commande
        $order = $orderServices->createOrder($cart);

        Stripe::setApiKey('sk_test_51Ju2k8CRBuwdGxzZgD0KNVGyFFM1oq7AMILXAAtssVljmB54juCrVh91zjjRRp7h4aH3QvnORgODBNQ183osQeln00Rugymg5X');
        
        //$line_items = $orderServices->getLineItems($cart);
        
        $checkout_session = Session::create([
            'customer_email' => $user->getEmail(), //Ici on recupère l'email de l'utilisateur
            //'payment_method_type' => ['card'],
            'line_items' => $orderServices->getLineItems($cart),
            'mode' => 'payment',
            'success_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-cancel/{CHECKOUT_SESSION_ID}',
          ]);
          
          //On recupère ici le checkout_session_id de notre commande
          $order->setStripeCheckoutSessionId($checkout_session->id);
          // On ne fera pas de persiste parce que l'objet existe déjà en Base de Donnée
          //Après ça on lance les differente commandes pour le pousser en base de DONNEE
          $manager->flush(); 

          
          header("HTTP/1.1 303 See Other");
          header("Location: " . $checkout_session->url);

          return $this->json(['id' => $checkout_session->id]);
    }
}
