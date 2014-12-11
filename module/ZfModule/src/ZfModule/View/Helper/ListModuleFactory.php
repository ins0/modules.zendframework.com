<?php

namespace ZfModule\View\Helper;

use EdpGithub\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;
use ZfModule\Mapper;

class ListModuleFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return ListModule
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var HelperPluginManager $serviceLocator */
        $sm = $serviceLocator->getServiceLocator();

        /* @var Mapper\Module $moduleMapper */
        $moduleMapper = $sm->get('zfmodule_mapper_module');

        /* @var Client $githubClient */
        $githubClient = $sm->get('EdpGithub\Client');

        return new ListModule($moduleMapper, $githubClient);
    }
}
