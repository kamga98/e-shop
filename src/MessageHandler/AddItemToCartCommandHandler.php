<?php

namespace App\MessageHandler;

use App\Message\AddItemToCartCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Factory\OrderItemFactory;  
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Webmozart\Assert\Assert;       
    
    
#[AsMessageHandler]
final class AddItemToCartCommandHandler{  
   
   // private CommandCartItemAdderInterface $cartItemAdder;
    private EntityManagerInterface $entityManager;
  
     public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderItemFactory $orderItemFactory,
        private OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        private OrderModifierInterface $orderModifier,
        private OrderProcessorInterface $orderProcessor 
    ) {    
     
    }     

    public function __invoke(AddItemToCartCommand $command): void
    {
      
        $productVariantRepository = $entityManager->getRepository(\App\Entity\Product\ProductVariant::class);
        
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['token_value' => $command->orderToken]);
        Assert::notNull($order, 'Panier non trouvé');

        $variant = $this->productVariantRepository->findOneBy(['code' => $command->productCode]);
        Assert::notNull($variant, 'Produit non trouvé');
  
        $orderItem = $this->orderItemFactory->createNew();     
        $orderItem->setVariant($variant);
        $orderItem->setUnitPrice($variant->getPrice()); // prix par défaut (si pas géré ailleurs)

        $this->orderItemQuantityModifier->modify($orderItem, $command->quantity);
        $this->orderModifier->addToOrder($order, $orderItem);
        $this->orderProcessor->process($order);  
      
       /* ✅ Internal Processing Only (No Visible Output)

         The method $this->orderProcessor->process() might do things like:  

         Save the order to a database

         Update inventory   
   
         Send a confirmation email

         Change order status

        */     

        $this->entityManager->flush(); // save changes permanently in the database.

       // dd("Mise à jour du panier dans la bdd dans le fichier AddItemToCartCommandHandler"); 
              
    }

    
   
    // public function __construct(
    //     CommandCartItemAdderInterface $cartItemAdder,
    //     EntityManagerInterface $entityManager
    // ) {
    //     $this->cartItemAdder = $cartItemAdder;  
    //     $this->entityManager = $entityManager;
    // }

 
    //  public function __invoke(AddItemToCartCommand $message): void
    // {
    //     // do something with your message     
    //     $cart = $this->cartItemAdder->add(
    //         $message->orderToken,
    //         $message->unitPrice,
    //         $message->quantity   
    //     );    
        
    //     // Si `add(...)` ne retourne pas le panier, tu peux le récupérer via le token/context
      
    //     // Puis persister
    //     $this->entityManager->flush();
    // } 

}


