<?php

namespace ZfModule\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;
use ZfModule\Mapper;

class NewModuleFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return NewModule
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var HelperPluginManager $serviceLocator */
        $sm = $serviceLocator->getServiceLocator();

        /* @var Mapper\Module $moduleMapper */
        $moduleMapper = $sm->get('zfmodule_mapper_module');

        return new NewModule($moduleMapper);
    }
}
