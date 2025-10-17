<?php 

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

class CartAddListener
{
    private CartContextInterface $cartContext;
    private OrderProcessorInterface $orderProcessor;
  
    public function __construct(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->cartContext = $cartContext;
        $this->orderProcessor = $orderProcessor;
    }

    public function onKernelController(ControllerEvent $event): void
    {    
        $request = $event->getRequest();
       
        if ($request->attributes->get('_route') === 'sylius_shop_ajax_cart_add_item') 
        {     
            dd('[CartAddListener] Listener déclenché ✔️');
        }
               
  
        // Vérifie si ce n'est pas la route "add to cart"
        if ($request->attributes->get('_route') !== 'sylius_shop_ajax_cart_add_item') {
            return;
        }

        // ⚠️ Le panier n’est pas encore modifié ici, mais on peut marquer pour traitement
        $request->attributes->set('_should_process_cart_total', true);

        dd("route sylius_shop_ajax_cart_add_item");   
    }

}
