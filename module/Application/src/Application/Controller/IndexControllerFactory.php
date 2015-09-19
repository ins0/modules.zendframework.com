<?php

namespace Application\Controller;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfModule\Service;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return IndexController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /* @var ControllerManager $controllerManager */
        $serviceManager = $controllerManager->getServiceLocator();

        /* @var Service\Module $moduleService */
        $moduleService = $serviceManager->get(Service\Module::class);

        return new IndexController($moduleService);
    }
}
