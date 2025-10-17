<?php 

namespace App\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;  
use Doctrine\ORM\Event\PostFlushEventArgs;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

class OrderTotalUpdaterListener
{   
    
    private OrderProcessorInterface $orderProcessor;

    /** @var OrderInterface[] */
    private array $ordersToUpdate = [];

    public function __construct(OrderProcessorInterface $orderProcessor)
    {
        $this->orderProcessor = $orderProcessor;
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof OrderInterface) {
                $this->ordersToUpdate[] = $entity;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->ordersToUpdate)) {
            return;
        }

        $em = $args->getEntityManager();

        foreach ($this->ordersToUpdate as $order) {
            $this->orderProcessor->process($order);
            $em->persist($order);
        }

        // Important: vider la liste avant le flush
        $this->ordersToUpdate = [];

        // Ce flush est OK ici car on ne le fait qu'une fois, hors du flush d'origine
        $em->flush();
    }

        // private $orderProcessor;

    // public function __construct(OrderProcessorInterface $orderProcessor)
    // {
    //     $this->orderProcessor = $orderProcessor;  
    // }
    
    // public function postFlush(PostFlushEventArgs $args): void
    // {
    //     $em = $args->getEntityManager();

    //     // Trouver les commandes modifiées (panier) et met à jour dans la base de données la première
    //     // commande modifier. "break" arrêtera la boucle après la première sauvegarde.     
    //     foreach ($em->getUnitOfWork()->getScheduledEntityUpdates() as $entity) {
    //         if ($entity instanceof OrderInterface) {
    //             // Recalculer le total
    //             $this->orderProcessor->process($entity);

    //             // Puis persister les changements
    //             $em->persist($entity);  
    //             $em->flush();
        
    //             break; // On effectue une seule sauvegarde.   
    //         }
    //     }
    //         dd("Listener called");  
    // }


}

