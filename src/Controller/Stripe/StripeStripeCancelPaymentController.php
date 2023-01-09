<?php

namespace App\Controller\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeStripeCancelPaymentController extends AbstractController
{
    /**
     * @Route("stripe-payment-cancel", name="stripe_payment_cancel")
     */
    public function index(): Response
    {
        return $this->render('stripe_stripe_cancel_payment/index.html.twig', [
            'controller_name' => 'StripeStripeCancelPaymentController',
        ]);
    }
}
