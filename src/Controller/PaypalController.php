<?php
   
namespace App\Controller;  
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sylius\Component\Order\Context\CartContextInterface;  
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;  
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use Psr\Log\LogLevel;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\AmountBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\ItemBuilder;
use PaypalServerSdkLib\Models\ItemCategory;
use PaypalServerSdkLib\Models\Builders\ShippingDetailsBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingNameBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingOptionBuilder;
use PaypalServerSdkLib\Models\ShippingType;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Models\Builders\PaypalWalletBuilder;
use PaypalServerSdkLib\Models\Builders\PaypalWalletExperienceContextBuilder;
use PaypalServerSdkLib\Models\ShippingPreference;
use PaypalServerSdkLib\Models\PaypalExperienceLandingPage;
use PaypalServerSdkLib\Models\PaypalExperienceUserAction;
use PaypalServerSdkLib\Models\Builders\CallbackConfigurationBuilder;
use PaypalServerSdkLib\Models\Builders\PhoneNumberWithCountryCodeBuilder;
use PaypalServerSdkLib\Models\Builders\PaymentSourceBuilder;
use PaypalServerSdkLib\Models\CallbackEvents;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderItemRepository as ORMOrderItemRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;   
use Symfony\Component\HttpFoundation\JsonResponse; 
use Sylius\Component\Order\Processor\OrderProcessorInterface; 
use App\Entity\Order\OrderItem;
use Exception;

final class PaypalController extends AbstractController
{
    // LES ETAPES DU PROCESSUS DE PAIEMENT    
    // 1. Affichage bouton PayPal (paypal.js)
    // 2. L'utilisateur clique dessus
    // 3. --> PayPal appelle createOrder()
    //     --> createOrder envoie le panier à /api/orders (via la méthode POST)
    //     --> Le backend crée la commande avec PayPal et retourne un ID (orderID)
    // 4. --> PayPal affiche sa fenêtre de paiement
    // 5. L'utilisateur valide le paiement
    // 6. --> PayPal appelle onApprove(data, actions)
    //   --> onApprove capture le paiement via /api/orders/:orderID/capture
    //   --> Tu affiches le message de succès ou d’erreur
  
          
    private ORMOrderItemRepository $orderItemRepository;
    private $PAYPAL_CLIENT_ID;  
    private $PAYPAL_CLIENT_SECRET; 
    private $client; 
   
    public function __construct(ORMOrderItemRepository $orderItemRepository)
    {    
            
      //  $this->PAYPAL_CLIENT_ID = getenv("PAYPAL_CLIENT_ID");
        $this->PAYPAL_CLIENT_ID = "ASAPZ1xxwEQx-Kdr2s6rtj70ExFrqbwPIJ3Vh_Qstmu2q51oPknbQvspagXSKe5fiM85-fowDFGICyQb";   

       // $this->PAYPAL_CLIENT_SECRET = getenv("PAYPAL_CLIENT_SECRET");
        $this->PAYPAL_CLIENT_SECRET = "ENhHLvKNmfCapGfQYJY5KWnOpVx60BKzkBmqgTNJj--E621ad60GoeqIW-xIGLRVGVOyQyTwlNHVA0LN";

        $this->orderItemRepository = $orderItemRepository;    
            
        $this->client = PaypalServerSdkClientBuilder::init()
                    ->clientCredentialsAuthCredentials(
                        ClientCredentialsAuthCredentialsBuilder::init(
                            $this->PAYPAL_CLIENT_ID,
                            $this->PAYPAL_CLIENT_SECRET
                )          
                )  
                ->environment(Environment::SANDBOX)
                ->build();
 
    }
   
       
    /* Pour savoir la structure de données utilisée par le panier juste avant la transmission
    de ses données au projet Node.js, nous avons dû appeller la fonction getCart() via la
    fonction dd() :  dd("Données du panier ...................", $this->getCart($cartContext));     
    */ 
      
    #[Route('{_locale}/checkout/address', name: 'sylius_shop_checkout_address', methods: ['GET', 'PUT'])]
    public function checkout(Request $request, CartContextInterface $cartContext): Response 
    {    
                 
             
      //  dd("Données du panier ...................", $this->getCart($cartContext));     
       
         
        // En plus de faire le lien entre cette méthode et le fichier index.html qui affiche le bouton de paiement
        // on devra transmettre les infos de paiement au fichier index.js.  
        return new RedirectResponse('http://localhost:3001');
  
            
        // Quand je clique sur le bouton checkout, une requête qui contient le panier est transmise
        // à ce contrôleur. Nous pouvons donc transmettre à la méthode render() le panier pour
        // que le fichier paypal.js puisse avoir accès au panier. C'est un algorithme possible. 
   
        $cart = $cartContext->getCart(); // Obtenir le panier actuel
        // $items = $cart->getItems(); // Collection d'OrderItem 
   
        // dd(get_class($cart));
        // dd('Monnaie',  $cart->getCurrencyCode());    
                      
     
        // Nous ne pouvons pas compter le nombres de produits d'une commande qui n'a pas encore été sauvegardée
        // en utilisant la variable EntityManagerInterface. Ce n'est pas très logique. 
        // Nous devons trouver un moyen de le faire via la variable $request.   
               
    
        // Le client peut acheter des produits de nature différents (robes, jeans, etc.).
        // La variable cart de paypal.js contiendra chaque produit et sa quantité. 
        // Nous devons trouver un moyen de transmettre à cette variable les bonnes données.      
    

        // dd("array of items", $items); 
   

        return $this->render('paypal/checkout.html.twig', [  
            'PAYPAL_CLIENT_ID' => $this->PAYPAL_CLIENT_ID,
            'cart' => $cart 
        ]);      
  
    }      


       
    #[Route('/api/orders', name: 'api_orders', methods: ['POST'])]  
    public function api_orders() 
    {
  
        // On récupère le panier envoyé par la méthode createOrder() du fichier paypal.js 
        $data = json_decode(file_get_contents("php://input"), true);
        $cart = $data["cart"];
        header("Content-Type: application/json");
   
           
        // try {
        //     // On appelle la méthode createOder() de notre contrôleur.   
        //     $orderResponse = $this->createOrder($cart);
        //     echo json_encode($orderResponse["jsonResponse"]);
        // } catch (Exception $e) {
        //     echo json_encode(["error" => $e->getMessage()]);
        //     http_response_code(500);
        // }   
        
        try {   
            // Création de la commande
            $orderResponse = $this->createOrder($cart);

            // Vérification de la présence de l'ID de la commande
            if (isset($orderResponse["jsonResponse"]["id"])) {
                echo json_encode([
                    "id" => $orderResponse["jsonResponse"]["id"]
                ]);      
            } else {
                echo json_encode(["error" => "ID de commande manquant dans la réponse PayPal."]);
                http_response_code(500);
            }
        } catch (\Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
            http_response_code(500);
        }
               
        error_log("✅ [DEBUG] Méthode api_orders appelée");
  
    }  

    /**
    * Create an order to start the transaction.
    * @see https://developer.paypal.com/docs/api/orders/v2/#orders_create
    */
    public function createOrder($cart) 
    {      
          
        /*
        La variable $cart permettra d'indiquer les vrais données de paiement (currency, montant). 
        Selon chatGPT, cette méthode doit renvoyer l'id de la commande (du panier) pour que le processus d'achat
        continue. Cette id devra être nommée "orderID".     
        */  
          
        /* 
        PayPal ne veut qu’un seul orderBody qui contient :
        Un seul purchase_unit (en général),
        Une liste de tous les articles dans un tableau items,
        Un montant total pour tous ces articles (amount),
        Et un champ item_total qui est la somme des prix × quantités.
        */   
            

        // Doit-on transmettre le prix en centimes ou conserver la valeur initiale ? 
        // Réponse de chatGPT : Non, je dois donc diviser le prix de chaque article par 100. 
        // 53,63 $ est actuellement sauvegardée comme 5363.  
           
        // Test 1 : Nous construisons le tableau d'items en respectant la structure de données exigée par paypal.     
        $items = [];   
        $total = 0.00;
        $currency = $cart->getCurrencyCode();

        foreach ($cart->getItems() as $item) {
            $unitPrice = $item->getUnitPrice() / 100;
            $quantity = $item->getQuantity();
            $lineTotal = $unitPrice * $quantity;

            $total += $lineTotal;

            // Nous construisons le tableau d'items en respectant la structure de données exigée par paypal.   
            $items[] = ItemBuilder::init(
                $item->getProductName(),
                MoneyBuilder::init($currency, number_format($unitPrice, 2, '.', ''))->build(),
                (string) $quantity
            )
                ->description("Produit ajouté au panier")
                ->sku($item->getVariant())
                ->build();
        }  

        $itemsTotal =  number_format($cart->getItemsTotal(), 2, '.', '');   
           
            $orderBody = [   
                "body" => OrderRequestBuilder::init("CAPTURE", [
                    PurchaseUnitRequestBuilder::init(
                        AmountWithBreakdownBuilder::init($cart->getCurrencyCode(), $itemsTotal)
                            ->breakdown(  
                                AmountBreakdownBuilder::init()    
                                    ->itemTotal(
                                        MoneyBuilder::init($cart->getCurrencyCode(), $itemsTotal)->build()
                                    )
                                    ->build()      
                            )  
                            ->build()
                        )    
                        // lookup item details in `cart` from database
                        // Nous devons inclure tous les produits du panier dans le tableau items[]   
                        ->items(
                              $items                          
                           )

                        ->build(),
                ])
                ->build(),
            ];

        // sku signifie Stock Keeping Unit.
        // C’est un identifiant unique (souvent alphanumérique) utilisé dans les systèmes de gestion de stock pour identifier un produit spécifique.
     
    // Création de la commande via l'API PayPal
    $apiResponse = $this->client->getOrdersController()->createOrder($orderBody);

    // La méthode handleResponse ci-dessous est appelée.  
    return handleResponse($apiResponse);
  
    }
         
    
    // Avec $this->handleResponse nous pouvons appeler cette méthode   
    public function handleResponse($apiResponse)
    {
        $jsonResponse = json_decode($apiResponse->getBody(), true);

            // Vérification de la réponse de l'API
        if ($apiResponse->getStatusCode() === 201) {
            // Extraction de l'ID de la commande
            $jsonResponse = json_decode($apiResponse->getBody(), true);
            return [
                "jsonResponse" => $jsonResponse,
                "httpStatusCode" => $apiResponse->getStatusCode(),
            ];
        } else {
            throw new \Exception("Erreur lors de la création de la commande PayPal.");
        } 
        
        // return [
        //     "jsonResponse" => $jsonResponse,
        //     "httpStatusCode" => $response->getStatusCode(),
        // ];
    }

      

    /**
     * Capture payment for the created order to complete the transaction.
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_capture
     */
    public function captureOrder($orderID)
    {
      
        $captureBody = [ 
            "id" => $orderID,
        ];     

        $apiResponse = $this->client->getOrdersController()->captureOrder($captureBody);

        return handleResponse($apiResponse);
    }


    #[Route('/capture', name: 'capture')]
    public function capture(Request $request) 
    {
        $endpoint = $request->getPathInfo();  
      
        if (str_ends_with($endpoint, "/capture")) {
            $urlSegments = explode("/", $endpoint);
            end($urlSegments); // Will set the pointer to the end of array
            $orderID = prev($urlSegments);
            header("Content-Type: application/json");
            try {
                $captureResponse = captureOrder($orderID);
                echo json_encode($captureResponse["jsonResponse"]);
            } catch (Exception $e) {
                echo json_encode(["error" => $e->getMessage()]);
                http_response_code(500);
            }
  
        }
    }         
  

    /* Dans le projet paypal-checkout-main-2, on cette ligne  const response = await fetch('http://localhost:8000/api/cart');
     qui permet d'appeller cette fonction et de récupérer les propriétés currency et total.    
    */
    #[Route('/api/cart', name: 'api_cart', methods: ['GET'])]
    public function getCartDatas(CartContextInterface $cartContext, OrderProcessorInterface $orderProcessor
    ): JsonResponse  
    {
    
        $cart = $cartContext->getCart(); // objet Order (le panier)  
        $currency = $cart->getCurrencyCode(); // récupère la devise

      
        // // Je dois aussi prendre en compte les frais de livraison dans le total final. 
        // // Je le ferai plus tard.  
        // $items = [];  
        $total = 0.0;  
   
              
        // foreach ($cart->getItems() as $item) {
        //     $unitPrice = $item->getUnitPrice() / 100; // Prix unitaire en euros (ou dollars)
        //     $quantity = $item->getQuantity();
        //     $lineTotal = $unitPrice * $quantity;

        //     $total += $lineTotal;

        //     $items[] = [
        //         'name' => $item->getProductName(),
        //         'unit_price' => number_format($unitPrice, 2, '.', ''), // formaté en chaîne avec 2 décimales
        //         'quantity' => $quantity,
        //         'sku' => $item->getVariant(), // ou $item->getVariant()->getCode() si tu veux un identifiant lisible
        //     ];   
        // }   


        // Si tu préfères utiliser le total du panier fourni directement par Sylius :
       // $total = number_format($cart->getItemsTotal() / 100, 2, '.', '');

                     
        // Dans le fichier index.js du projet Paypal on récupère exactement ces propriétés (currency, code).
        // Les noms des deux propriétés sont exactements les mêmes donc on ne pourrait
        // pas avoir une erreur liée au nommage des propriétés.
        // Dans la console de Git sous "npm run start", on peut voir le résultat de console.log(data);
        // qui affiche les données transmises par le "return" ci-dessous :       
        return new JsonResponse([   
            'currency' => $currency,
            'total' => $total                 
        ]);    
    }   


    
    #[Route('/', name: 'app_paypal')]
    public function messageOfTheServer() 
    {
        try {   
        $response = [
            "message" => "Server is running",
        ];
        header("Content-Type: application/json");
        echo json_encode($response);
            } catch (Exception $e) {
                echo json_encode(["error" => $e->getMessage()]);
                http_response_code(500);
            }
    }
      
      
   
    #[Route('/paypal', name: 'app_paypal')]
    public function index(): Response
    {
        return $this->render('paypal/index.html.twig', [
            'controller_name' => 'PaypalController',
        ]);
    }
    
     
    /* GOOD TO KNOW : 

     Dans ce code : number_format($unitPrice, 2, '.', ''), la fonction number_format() : 
     Elle formate un nombre décimal (par exemple un prix) dans un format compatible avec l'API PayPal, qui :

    Attend une chaîne de caractères (string)

    Avec exactement deux décimales

    Avec le point . comme séparateur décimal

    Et aucun séparateur de milliers    


    */ 
   



}


      