<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sylius\Component\Order\CartActions;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Form\Factory\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;  
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class OrderItemController extends AbstractController
{

    //  public function __construct(
    //     private RequestConfigurationFactoryInterface $requestConfigurationFactory,
    //     private FactoryInterface $newResourceFactory,
    //     private FormFactoryInterface $resourceFormFactory,  
    //     private EntityManagerInterface $entityManager,
    //     private OrderProcessorInterface $orderProcessor,    
    //     private CartContextInterface $cartContext,
    //     private RepositoryInterface $orderItemRepository,
    // ) {}

    // public function addAction(Request $request): Response
    // {
    //     $metadata = new Metadata('sylius.order_item'); // ← solution propre ici
    //     $configuration = $this->requestConfigurationFactory->create($metadata, $request);

    //     /** @var OrderItemInterface $orderItem */
    //     $orderItem = $this->newResourceFactory->create($configuration);

    //     $form = $this->resourceFormFactory->create($configuration, $orderItem);
    //     $form->handleRequest($request);

    //     if (!$form->isSubmitted() || !$form->isValid()) {
    //         return new JsonResponse([
    //             'success' => false,
    //             'errors' => (string) $form->getErrors(true, false),
    //         ], Response::HTTP_BAD_REQUEST);
    //     }

    //     $this->entityManager->persist($orderItem);
    //     $this->entityManager->flush();

    //     /** @var OrderInterface $cart */
    //     $cart = $this->cartContext->getCart();
    //     $this->orderProcessor->process($cart);

    //     return new JsonResponse([
    //         'success' => true,
    //         'total' => $cart->getTotal(),
    //         'currency' => $cart->getCurrencyCode(),
    //     ]);
    // }

    // public function removeAction(Request $request): Response
    // {
    //     $metadata = new Metadata('sylius.order_item'); // idem ici
    //     $configuration = $this->requestConfigurationFactory->create($metadata, $request);

    //     $id = $request->attributes->get('id');
    //     /** @var OrderItemInterface|null $orderItem */
    //     $orderItem = $this->orderItemRepository->find($id);

    //     if (!$orderItem) {
    //         return new JsonResponse(['error' => 'Item not found'], Response::HTTP_NOT_FOUND);
    //     }

    //     /** @var OrderInterface $cart */
    //     $cart = $this->cartContext->getCart();
    //     $cart->removeItem($orderItem);

    //     $this->entityManager->remove($orderItem);
    //     $this->entityManager->flush();

    //     $this->orderProcessor->process($cart);

    //     return new JsonResponse([
    //         'success' => true,
    //         'total' => $cart->getTotal(),  
    //         'currency' => $cart->getCurrencyCode(),
    //     ]);
    // }


    // #[Route('/order/item', name: 'app_order_item')]
    // public function index(): Response
    // {
    //     return $this->render('order_item/index.html.twig', [
    //         'controller_name' => 'OrderItemController',
    //     ]);  
    // }
}
