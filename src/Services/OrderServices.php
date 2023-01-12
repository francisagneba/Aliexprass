<?php
namespace App\Services;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\CartDetails;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;



class OrderServices{

    private $manager;
    private $repoProduct;

    public function __construct(EntityManagerInterface $manager , ProductRepository $repoProduct)
    {
        $this->manager = $manager;
        $this->repoProduct = $repoProduct;
    }

    // Cette methode cree une commande, cela consiste a enregistrer la commende dans la DB
    public function createOrder($cart){
        
        //on initialise l'ordre ou la commande
        $order = new Order();
        //Ensuite on va prendre les attribute de l'order ou commande
        $order->setReference($cart->getReference())
              ->setCarrierName($cart->getCarrierName())
              ->setCarrierPrice($cart->getCarrierPrice()/100)
              ->setFullName($cart->getFullName())
              ->setDeliveryAddress($cart->getDeliveryAddress())
              ->setMoreInformations($cart->getMoreInformations())
              ->setQuantity($cart->getQuantity())
              ->setSubTotalHT($cart->getSubTotalHT()/100)
              ->setTaxe($cart->getTaxe()/100)
              ->setSubTotalTTC($cart->getSubTotalTTC()/100)
              ->setUser($cart->getUser())
              ->setcreatedAt($cart->getCreatedAt());
        $this->manager->persist($order); 

        //après ça on recupère les details de la commande
        //on recupère les information sur le detail du panier
        $products = $cart->getCartDetails()->getValues();
        
        //en faisant une boucle on a la valeur de chaque details dans $productet on le
        // fait passer dans $cart_product
        foreach ($products as $cart_product) {
            //on initialise le detail de la commande en créeant une Instance de la classe
            $orderDetails = new OrderDetails();
            $orderDetails->setOrders($order)
                         ->setProductName($cart_product->getProductName())
                         ->setProductPrice($cart_product->getProductPrice())
                         ->setQuantity($cart_product->getQuantity())
                         ->setSubTotalHT($cart_product->getSubTotalHT())
                         ->setSubTotalTTC($cart_product->getSubTotalTTC())
                         ->setTaxe($cart_product->getTaxe());
            $this->manager->persist($orderDetails);
        }
        
        //Après avoir envoyer cela dans la base de donnée on fait un Return de la commande
        $this->manager->flush();

        return $order;

    }

    public function getLineItems($cart){
        $cartDetails = $cart->getCartDetails();

        $line_items = [];
        foreach ($cartDetails as $details) {
            $product = $this->repoProduct->findOneByName($details->getProductName());

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
                'quantity' => $details->getQuantity(),
               ];
        }

        //Carrier
        $line_items[] = [
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            'price_data' => [
              'currency' => 'usd',
              'unit_amount' => $cart->getCarrierPrice(),
              'product_data' => [
                'name' => 'Carrier ( '.$cart->getCarrierName().')',
                'images' => [$_ENV['YOUR_DOMAIN'].'/uploads/products/'],
              ]
            ],
            'quantity' => 1,
        ];

        //taxe
        $line_items[] = [
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            'price_data' => [
              'currency' => 'usd',
              'unit_amount' => $cart->getTaxe(),
              'product_data' => [
                'name' => 'TVA (20%)',
                'images' => [$_ENV['YOUR_DOMAIN'].'/uploads/products/'],
              ]
            ],
            'quantity' => 1,
        ];

           return $line_items;

    }

    public function saveCart($data, $user){

        /*[
            'products' => [],
            'data' => [],
            'checkout' => [
                'address' => Objet,
                'carrier' => objet,
                'information' =>String
            ]
        ]*/

         $cart = new Cart();
         $reference = $this->generateUuid();
         $address = $data['checkout']['address'];
         $carrier = $data['checkout']['carrier'];
         $informations = $data['checkout']['informations'];

         $cart->setReference($reference)
              ->setCarrierName($carrier->getName())
              ->setCarrierPrice($carrier->getPrice()/100)
              ->setFullName($address->getFullName())
              ->setDeliveryAddress($address)
              ->setMoreInformations($informations)
              ->setQuantity($data['data']['quantity_cart'])
              ->setSubTotalHT(($data['data']['subTotalHT']))
              ->setTaxe($data['data']['taxe'])
              ->setSubTotalTTC(round(($data['data']['subTotalTTC']+$carrier->getPrice()/100),2))
              ->setUser($user)
              ->setcreatedAt(new \DateTimeImmutable());

            $this->manager->persist($cart);

            $cart_details_array = [];

            foreach ($data['products'] as $products) {
                $cartDetails = new CartDetails();

                $subtotal = $products['quantity'] * $products['product']->getPrice()/100;

                $cartDetails->setCarts($cart)
                            ->setProductName($products['product']->getName())
                            ->setProductPrice($products['product']->getPrice()/100)
                            ->setQuantity($products['quantity'])
                            ->setSubTotalHT($subtotal)
                            ->setSubTotalTTC($subtotal*0.2)
                            ->setTaxe($subtotal*0.2);

                $this->manager->persist($cartDetails);
                $cart_details_array[] = $cartDetails;            

            }

            $this->manager->flush();

            return $reference;


    }
    
    //Le role de cette methode va nous permettre de generer un identifiant 
    //unique pour sauvegarder les commandes des clients.
    public function generateUuid(){

        //Initialise le generateur de nombres aléatoires Mersenne Twister
        mt_srand((double)microtime()*100000);

        //strtoupper: renvoie une chaine en majuscule
        //uniqid : genere un identifiant unique
        $charid = strtoupper(md5(uniqid(rand(), true)));

        //Générer une chaine d'un octet à partir d'un nombre
        $hyphen = chr(45);

        //substr : retourne un segment de chaine
        $uuid = ""
        .substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid, 12, 4).$hyphen
        .substr($charid, 16, 4).$hyphen
        .substr($charid, 20, 12);

        return $uuid;
    }
}
