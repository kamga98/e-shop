<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;  
use Symfony\Component\HttpFoundation\RedirectResponse;      
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;   
use Mollie\Api\MollieApiClient;   
   
   
final class PaymentRedirectController extends AbstractController
{

    private MollieApiClient $mollie;
  
     
    public function __construct()
    {  
       $this->mollie = new MollieApiClient();
 
       $this->mollie->setApiKey("test_rmhBV2hB2edSbfnuE59HNubSTjVMEE");
         
    }      
      

    #[Route('/payment/redirect', name: 'app_payment_redirect')]
    public function index(): Response
    {
        return $this->render('payment_redirect/index.html.twig', [
            'controller_name' => 'PaymentRedirectController',
        ]);
    }

       
    /* Lorsque l'utilisateur est sur la page dont l'adresse url est http://127.0.0.1:8000/en_US/cart/
    et qu'il clique sur le bouton "Next", cette fonction est appelée.
    Le client pourra transmettre son adresse postale par courriel au marchand et ce dernier
    mettra à jour cette information manuellement dans la base de données, s'il désire la stocker. 
    Il pourra ainsi la transmettre à l'agence de livraison de la commande.                  
    */      
     #[Route('/{_locale}/checkout/address', name: 'sylius_shop_checkout_address', methods: ['GET', 'PUT'])]
    public function redirectToMollie(Request $request): RedirectResponse
    {       
                      
        /* Nous devrons faire en sorte que les données de la variable $payment 
           correspondent à celle de la commande du client. */
         $payment = $this->mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => "10.00"
            ],
            "description" => "Test payment with Mollie",
            "redirectUrl" => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);  

          
        // Peut-on récupérer les données de la requête pour les transmettre au formulaire ?
        // dd("request ===>", $request);    

        // Redirige l'utilisateur vers Mollie. 
        return new RedirectResponse($payment->getCheckoutUrl(), 303);
   

   
        /* Résultat attendu : 
        - Le client mentionne son adresse. 
        - Il clique sur Next.
        - Sylius passe normalement à /en_US/checkout/address mais notre surchage (notre contrôleur)
        intercepte la requête. 
        - Le client est redirigé automatiquement vers Mollie. 

        */ 
          
     }
  
      
}


  