<?php

namespace App\Controller\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeStripeSuccessPaymentController extends AbstractController
{
    /**
     * @Route("stripe-payment-success", name="stripe_payment_success")
     */
    public function index(): Response
    {
        return $this->render('stripe_stripe_success_payment/index.html.twig', [
            'controller_name' => 'StripeStripeSuccessPaymentController',
        ]);
    }
}
