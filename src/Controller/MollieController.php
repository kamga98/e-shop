<?php

namespace App\Controller;  
  
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface; 
 use Symfony\Component\HttpFoundation\RedirectResponse;      
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;  
use Mollie\Api\MollieApiClient; 

final class MollieController extends AbstractController
{
  
    private MollieApiClient $mollie;
  
    public function __construct()
    {  
        $this->mollie = new MollieApiClient();
 
      // $api_key = getenv('MOLLIE_KEY'); 
      // dd("API KEY =>", $api_key);            
      //  $this->mollie->setApiKey(getenv('MOLLIE_KEY'));
        
      /* We put directly "test_rmhBV2hB2edSbfnuE59HNubSTjVMEE" just for a test.  
        Normally, it must be getenv('MOLLIE_KEY') 
      */
       $this->mollie->setApiKey("test_rmhBV2hB2edSbfnuE59HNubSTjVMEE");
         
    }      
      
      
    // Crée un paiement et redirige l’utilisateur vers Mollie
    // Le nom "SANTIAGO MARTINEZ KAMGA" est indiqué en guise de titre du formulaire car
    // le marchand est le titulaire du compte Mollie : SANTIAGO MARTINEZ KAMGA dans notre cas.
    // Ce dernier est lié au formulaire via la clé api : test_rmhBV2hB2edSbfnuE59HNubSTjVMEE 
    #[Route('/test_with_mollie', name: 'mollie_test')]
    public function preparePayment()
    {      
        
        /* La propriété "paymeny_success" de redirectUrl permet d'éxécuter le code de la fonction
        nommée payment_success.  
        */  
        $payment = $this->mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => "10.00"
            ],
            "description" => "Test payment with Mollie",
            "redirectUrl" => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);  
  
  
        /* Le code de statut de réponse de redirection 303 See Other, 
        généralement renvoyé comme résultat d'une opération PUT ou POST, 
        indique que la redirection ne fait pas le lien vers la ressource 
        nouvellement téléversée mais vers une autre page (par exemple une page 
        de confirmation ou qui affiche l'avancement du téléversement). 
        La méthode utilisée pour afficher la page redirigée est toujours GET.
        Dans notre cas, la page affichée est la page de paiement.   
        */     
        return new RedirectResponse($payment->getCheckoutUrl(), 303);
 
    }
  
   
    // Callback de succès (après paiement)
    #[Route('/payment-success', name: 'payment_success')]
    public function paymentSuccess(Request $request)
    {
       
        dd("Payment successful!");    

        // Normally we have to go back the homepage of the sylius_shop_user  
        return new RedirectResponse("Payment successful!"); 

    }



    
    #[Route('/mollie', name: 'app_mollie')]
    public function index(): Response
    {
        return $this->render('mollie/index.html.twig', [
            'controller_name' => 'MollieController',
        ]);
    }
   
}

  

 