<?php

namespace App\Message;


final class AddItemToCartCommand
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

    // public function __construct(
    //     public readonly string $name,
    // ) {
    // }

    // The datas we need to solve the problem.  
    public int $cartToken;
    public string $productCode;
    public int $quantity;
        
   
    // Via le contrôleur, nous pourrons transmettre le token du panier, le prix unitaire et la quantité du produit choisi.
    // Le choix des mots est très important en sciences.  
    public function __construct($cartToken, $productCode, $quantity)
    {      
       $this->cartToken = $cartToken;  
       $this->productCode = $productCode;           
       $this->quantity = $quantity; 
        
    }         
                       
       
    // protected function configure(): void              
    // {
    //     $this
    //         ->addArgument('cartToken', InputArgument::REQUIRED, $this->cartToken) // token of the cart  
    //         ->addArgument('unitPrice', InputArgument::REQUIRED,  $this->unitPrice) // 'Unit price of the product to add'
    //         ->addArgument('quantity', InputArgument::OPTIONAL,  $this->quantity, 1);
    // }       
    
    // protected function execute(InputInterface $input, OutputInterface $output): int
    // {
    //     $cartToken = $input->getArgument('cartToken');
    //     $unitPrice = $input->getArgument('unitPrice');     
    //     $quantity = (int) $input->getArgument('quantity');

    //     $output->writeln("Adding $unitPrice (x$quantity) to cart with token $cartToken");
  
    //     // TODO: Appeler un handler ou un service pour ajouter au panier
   
    //     return Command::SUCCESS;
    // }

}
