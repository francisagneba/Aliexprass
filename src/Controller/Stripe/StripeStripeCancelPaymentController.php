<?php

namespace App\Controller\Stripe;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeStripeCancelPaymentController extends AbstractController
{
    /**
     * @Route("stripe-payment-cancel/{StripeCheckoutSessionId}", name="stripe_payment_cancel")
     */
    public function index(?Order $order): Response
    {
        //dd($order);
        // Si on n'arrive pas à recuperer la commande ou bien si l'utilisateur est different
        //de celui qui est connecté, on redirige vers la page d'acceuil
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute("app_home");
        }

        return $this->render('stripe_stripe_cancel_payment/index.html.twig', [
            'order' => $order,
        ]);
    }
}
