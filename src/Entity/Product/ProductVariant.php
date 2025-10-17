<?php

declare(strict_types=1);

namespace App\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\MolliePlugin\Entity\RecurringProductVariantTrait;
  
 
// use Sylius\MolliePlugin\Entity\ProductVariantInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_variant")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_product_variant')]
class ProductVariant extends BaseProductVariant implements ProductVariantInterface
{

    // Le plugin Mollie s’appuie sur ce trait pour gérer les produits à facturation récurrente (abonnements).
    // Nous devons revérifier l'information ci-dessus avec un site officiel.   
    use RecurringProductVariantTrait;  

    protected function createTranslation(): ProductVariantTranslationInterface
    {
        return new ProductVariantTranslation();
    }
}
  