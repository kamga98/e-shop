<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Message\AddItemToCartCommand;
use Symfony\Component\HttpFoundation\Request;   
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
   
    
final class AddToCartController extends AbstractController
{

    public function __construct(private MessageBusInterface $messageBus,  private ProductRepositoryInterface $productRepository) {}


    // Avec php bin/console debug:router on peut voir que notre méthode est liée à la route d'ajout de produits.   
    #[Route('/{_locale}/ajax/cart/{productId}', name: 'sylius_shop_ajax_cart_add_item', methods: ['POST'])]
    public function add(Request $request, string $productId): Response
    { 
    
        $productId = $request->query->get('productId');      
        $form = $request->request->all();       
        $query = $request->query->all();
              
    
        dd([     
            'query' => $query,  
            'form' => $form,
        ]);
     
  
       // dd('function add .....................................................');   
        
        try {               
            // $cartToken = $request->get('token');    
            // $productId = $request->get('productId');       
            // $quantity = (int) $request->get('quantity', 1);
  
            dd('function add', $cartToken, $productId, $quantity);  
             // Si l’ID n’a pas été passé dans l’URL, on le récupère dans la query string
            // $productId ??= $request->get('productId');
            // if (!$productId) {
            //     return new JsonResponse(['errors' => ['Aucun productId fourni.']], 400);
            // } 
           
             
            if (!$cartToken || !$productId) {  
                throw new \Exception('Paramètres manquants : token ou productId');
            }

            // On récupère le produit depuis la base
            $product = $this->productRepository->find($productId);
            if (!$product) {
                throw new \Exception("Produit non trouvé pour l'ID $productId");
            }

            // On récupère la première variante du produit
            $variant = $product->getVariants()->first();
            if (!$variant) {
                throw new \Exception("Aucune variante trouvée pour le produit ID $productId");
            }

            $productCode = $variant->getCode();

            // Envoi de la commande
            $command = new AddItemToCartCommand($cartToken, $productCode, $quantity);
            $this->messageBus->dispatch($command);

            return new JsonResponse(['success' => 'Produit ajouté au panier.']);

        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }

    }
      
         
    #[Route('/add/to/cart', name: 'app_add_to_cart')]
    public function index(): Response
    {
        return $this->render('add_to_cart/index.html.twig', [
            'controller_name' => 'AddToCartController',
        ]);
    }
}
