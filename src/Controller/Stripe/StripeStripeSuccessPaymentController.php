<?php

namespace App\Controller\Stripe;

use App\Entity\Order;
use App\Services\CartServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeStripeSuccessPaymentController extends AbstractController
{
    /**
     * @Route("stripe-payment-success/{StripeCheckoutSessionId}", name="stripe_payment_success")
     */
    public function index(?Order $order, CartServices $cartServices, EntityManagerInterface $manager): Response
    {
        //dd($order);
        // Si on n'arrive pas à recuperer la commande ou bien si l'utilisateur est different
        //de celui qui est connecté, on redirige vers la page d'acceuil
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute("app_home");
        }

        if (!$order->isIsPaid()) {
            //commande payée on madifie le false à true et on supprime le panier
            $order->setIsPaid(true);
            $manager->flush();
            $cartServices->deleteCart();
            //un mail au client
        }
        return $this->render('stripe_stripe_success_payment/index.html.twig', [
            'order' => $order,
        ]);
    }
}
