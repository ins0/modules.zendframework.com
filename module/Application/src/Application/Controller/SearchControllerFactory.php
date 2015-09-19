<?php

namespace Application\Controller;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfModule\Service;

class SearchControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return SearchController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /* @var ControllerManager $controllerManager */
        $serviceManager = $controllerManager->getServiceLocator();

        /* @var Service\Module $moduleService */
        $moduleService = $serviceManager->get(Service\Module::class);

        return new SearchController($moduleService);
    }
}
