<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;  
use App\Entity\User\AdminUser;
use App\kernel;   

final class PasswordResetController extends AbstractController
{

    
    /* We better use those cmmands to : 

         - Create an admin : php bin/console sylius:admin-user:create
         We'll choose the values of many fiels like : email, password, firstname, lastname.

         - Change the password of a user : php bin/console sylius:admin-user:change-password

    */

        
    // #[Route('/test', name: 'app_test',  methods: 'GET')]
    // public function test(    
    //     EntityManagerInterface $em, 
    //     UserPasswordHasherInterface $passwordHasher
    // ): Response {
    

    //     // Récupérer le repository directement via EntityManager
    //     $userRepository = $em->getRepository(AdminUser::class);

    //     // Chercher l'utilisateur avec username = 'api'
    //     $syliusAdminUser = $userRepository->findOneBy(['username' => 'calvin']);   
       
    //     // $syliusAdminUsers = $userRepository->findAll();    
    //     // dd($syliusAdminUsers);
 
          
    //     if (!$syliusAdminUser) {  
    //         return new Response('Utilisateur introuvable', 404);
    //     }
  
    //     // Nouveau mot de passe en clair (à générer aléatoirement en production)
    //     $newPlain = "admin";            

    //     // Hasher le mot de passe   
    //     /* Sylius (qui est basé sur Symfony) encode les mots de passe (en général avec bcrypt 
    //     ou argon2i) avant de les stocker. Si tu as mis à jour le mot de passe manuellement dans la base (par SQL ou autre), 
    //     et que tu as mis un mot de passe en clair, il ne sera pas reconnu.
    //     On doit encoder le mot de passe via l'encodeur Symfony avant de le stocker. 
    //     La fonction test ne résout pas notre problème. */ 
    //     $hashed = $passwordHasher->hashPassword($syliusAdminUser, $newPlain);
  
    //     // Mettre à jour le mot de passe encodé dans l'utilisateur
    //     $syliusAdminUser->setPassword($hashed);   

    //     // Sauvegarder la modification en base     
    //     $em->flush();

    //     return new Response('Mot de passe mis à jour pour l\'utilisateur "calvin".');
    // }


    
    // #[Route('/new_test', name: 'new_test')]
    // public function new_test()  
    // {
  
    //     $kernel = new Kernel('dev', true);
    //     $kernel->boot();
    //     $container = $kernel->getContainer();

    //     $adminUserRepository = $container->get('sylius.repository.admin_user');  
    //     $passwordEncoder = $container->get('security.user_password_hasher');

    //     // Remplace cet email par celui de l'utilisateur concerné  
    //     $user = $adminUserRepository->findOneBy(['username' => 'calvin']);   

    //     if (!$user) {
    //         echo "Utilisateur non trouvé\n";
    //         exit(1);  
    //     }  
    
    //     $password = 'nouveauMotDePasse';
    //     $encoded = $passwordEncoder->encodePassword($user, $password);
    //     $user->setPassword($encoded);

    //     $em = $container->get('doctrine.orm.entity_manager');
    //     $em->flush();

    //     echo "Mot de passe mis à jour avec succès\n";

    // }  

    /* Cette fonction permet de vérifier la valeur de la variable STRIPE_WEBHOOK_SECRET
     qu'on a stockée dans le fichier .env.local.
    */ 
    // #[Route('/test2', name: 'app_test2')]
    // public function test2(): Response {
  
    //     $secret1 = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? null;
    //     $secret2 = $_SERVER['STRIPE_WEBHOOK_SECRET'] ?? null;
         
    //    // dd($secret1, $secret2); // ou dump() + return

    //     return new Response('Vérification effectuée !');

    // }


    // #[Route('/password/reset', name: 'app_password_reset')]
    // public function index(): Response
    // {
    //     return $this->render('password_reset/index.html.twig', [
    //         'controller_name' => 'PasswordResetController',
    //     ]);
    // }   
}
