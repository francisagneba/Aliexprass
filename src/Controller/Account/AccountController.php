<?php

namespace App\Controller\Account;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="app_account")
     */
    public function index(OrderRepository $repoOrder): Response
    {
        //Ici on veut les commandes Payé de l'utilisateur qui est connecté, donc on a 2 critères.
        //On affichera les commandes par ordre Decroissant, on le mettra aussi dans un tableau 
        $orders = $repoOrder->findBy(['isPaid' => true, 'user' => $this->getUser()], ['id'=>'DESC']);

        //dd($orders);

        return $this->render('account/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/order/{id}", name="app_account_order_details")
     */
    public function show(?Order $order): Response
    {
        // Si on n'arrive pas à recuperer la commande ou bien si l'utilisateur est different
        //de celui qui est connecté, on redirige vers la page d'acceuil
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute("app_home");
        }
        
        return $this->render('account/detail_order.html.twig', [
            'order' => $order
        ]);
    }
}
