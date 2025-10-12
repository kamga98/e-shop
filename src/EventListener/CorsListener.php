<?php 
  
# src/EventListener/CorsListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class CorsListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
         $response = $event->getResponse();
         $response->headers->set('Access-Control-Allow-Origin', '*');
         $response->headers->set('Access-Control-Allow-Headers', 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method');
         $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
    }    
}
  