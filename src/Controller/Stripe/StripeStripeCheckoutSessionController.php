<?php

namespace App\Controller\Stripe;

use App\Services\CartServices;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeStripeCheckoutSessionController extends AbstractController
{
    /**
     * @Route("/create-checkout-session", name="create_checkout_session")
     */
    public function index(CartServices $cartService): JsonResponse
    {
        //on recupère tout le contenue du panier
        $cart = $cartService->getFullCart();

        Stripe::setApiKey('sk_test_51MN4BGLy6alHPRbUXegzPZqpp2nB2lLNIShdKwpneZkJvbi3MtsbEfBDmJG4fkJRR1Q0o2DAWBcgA0RFURdl8hRK006pXl4GlK');

        $line_items = [];
        foreach ($cart['products'] as $data_product) {

          /*[
              'quantity'=> 5,
              'product' => objet
            ]*/ 

            $product = $data_product['product'];

           $line_items[] = [
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            'price_data' => [
              'currency' => 'usd',
              'unit_amount' => $product->getPrice(),
              'product_data' => [
                'name' => $product->getName(),
                'images' => [$_ENV['YOUR_DOMAIN'].'/uploads/products/'.$product->getImage()],
              ]
            ],
            'quantity' => $data_product['quantity'],
           ];
        }
        
        $checkout_session = Session::create([
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-success',
            'cancel_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-cancel',
          ]);
          
          header("HTTP/1.1 303 See Other");
          header("Location: " . $checkout_session->url);

          return $this->json(['id' => $checkout_session->id]);
    }
}
