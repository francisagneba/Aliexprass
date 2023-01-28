<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SearchProduct;
use App\Form\SearchProductType;
use App\Repository\ProductRepository;
use App\Repository\HomeSliderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ProductRepository $repoProduct, HomeSliderRepository $repoHomeSlider): Response
    {
        $products = $repoProduct->findAll();

        $homeSlider = $repoHomeSlider->findBy(['isDisplayed'=>true]);

        //dd($homeSlider);

        $productBestSeller = $repoProduct->findByIsBestSeller(1);
        $productSpecialOffer = $repoProduct->findByIsSpecialOffer(1);
        $productNewArrival = $repoProduct->findByIsNewArrival(1);
        $productFeatured = $repoProduct->findByIsFeatured(1);

       // dd($productFeatured);
        return $this->render('home/index.html.twig', [
            //'controller_name' => 'HomeController',
            'products' => $products,
            'productBestSeller' => $productBestSeller,
            'productSpecialOffer' => $productSpecialOffer,
            'productNewArrival' => $productNewArrival,
            'productFeatured' => $productFeatured,
            'homeSlider' => $homeSlider,

        ]);
    }
    
    /**
     * @Route("/product/{slug}", name="product_details")
     */
    public function show(?Product $product): Response
    {
        if(!$product){//Si le produit n'est pas définit ou n'existe pas on redirige vers la page HOME.
            return $this->redirectToRoute("home");
        }
        return $this->render("home/single_product.html.twig", [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/shop", name="app_shop")
     */
    public function shop(ProductRepository $repoProduct, Request $request): Response
    {
        $products = $repoProduct->findAll();
        
        //On crée l'instance de notre Objet métier qu'on a créé ici.
        $search = new SearchProduct();

        $form = $this->createForm(SearchProductType::class, $search);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          
            $products = $repoProduct->findWithSearch($search);
        }

        return $this->render('home/shop.html.twig', [
          
            'products' => $products,
            'search' => $form->createView()

        ]);
    }
}
