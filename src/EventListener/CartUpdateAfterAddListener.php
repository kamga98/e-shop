<?php
    
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

class CartUpdateAfterAddListener
{
    public function __construct(
        private CartContextInterface $cartContext,
        private OrderProcessorInterface $orderProcessor,
        private EntityManagerInterface $entityManager,
    ) {}

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        // Est-ce qu’on a marqué cette requête ?
        if (!$request->attributes->get('_should_process_cart_total')) {
            return;
        }

        $cart = $this->cartContext->getCart();

        $this->orderProcessor->process($cart);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
          
        dd("CartUpdateAfterAddListener used");  
        
           
    }
}
