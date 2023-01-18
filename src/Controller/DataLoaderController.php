<?php

namespace App\Controller;

use App\Entity\Carrier;
use App\Entity\Categories;
use App\Entity\Product;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DataLoaderController extends AbstractController
{
    /**
     * @Route("/data", name="app_data_loader")
     */
    public function index(EntityManagerInterface $manager, UserRepository $repoUser): JsonResponse
    {
        //On recupère le fichier en sortant 2 fois puisse qu'on est dans AliExprass/src/conttroller
        //Alors que le fichier est à laracine du projet c'est à dire dans AliExprass
        $file_products = dirname(dirname(__DIR__))."\product.json";
        $file_categories = dirname(dirname(__DIR__))."\categories.json";


        //On va lire le fichier
        $data_products = json_decode(file_get_contents($file_products))[0]->rows;
        $data_categories = json_decode(file_get_contents($file_categories))[0]->rows;

        //On crèe une boucle pour récuperer les produits et les categorie

        $categories =[];
        //$products =[];

        foreach ($data_categories as $data_category) {
            $category = new Categories();
            $category->setName($data_category[1])
                     ->setImage($data_category[3]);
            //$manager->persist($category);
            $categories[] = $category;
        }

        /*$carriers =[];

        foreach ($data_carriers as $data_carrier) {
            $carrier = new Carrier();
            $carrier->setName($data_carrier[1])
                     ->setDescription($data_carrier[2])
                     ->setPrice($data_carrier[3]);
            //$manager->persist($category);
            $carriers[] = $carrier;
        }*/



        foreach ($data_products as $data_Product) {
            $product = new Product();
            $product->setName($data_Product[1])
                    ->setDescription($data_Product[2])
                    ->setPrice($data_Product[4])
                    ->setIsBestSeller($data_Product[5])
                    ->setIsNewArrival($data_Product[6])
                    ->setIsFeatured($data_Product[7])
                    ->setIsSpecialOffer($data_Product[8])
                    ->setImage($data_Product[9])
                    ->setQuantity($data_Product[10])
                    ->setTags($data_Product[12])
                    ->setSlug($data_Product[13])
                    ->setCreatedAt(new \DateTimeImmutable());
            //$manager->persist($product);
            $products[] = $product;
        }
        
       // $user = $repoUser->find(1);
       // $user->setRoles(['ROLE_ADMIN']);

       // $manager->flush();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DataLoaderController.php',
        ]);
    }
}
