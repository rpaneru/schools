<?php
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ApiPathPlugin extends AbstractPlugin 
{
    public function __invoke() 
    {
        return 'http://schools-api/';
    }
}