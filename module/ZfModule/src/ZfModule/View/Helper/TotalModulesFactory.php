<?php

namespace ZfModule\View\Helper;

use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;
use ZfModule\Service;

class TotalModulesFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return TotalModules
     */
    public function createService(ServiceLocatorInterface $helperPluginManager)
    {
        /* @var HelperPluginManager $helperPluginManager */
        $serviceLocator = $helperPluginManager->getServiceLocator();

        /* @var Module $moduleService */
        $moduleService = $serviceLocator->get(Service\Module::class);

        return new TotalModules($moduleService);
    }
}
