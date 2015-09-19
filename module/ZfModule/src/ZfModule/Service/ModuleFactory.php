<?php

namespace ZfModule\Service;

use Doctrine\ORM\EntityManager;
use EdpGithub\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfModule\Entity;

class ModuleFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return Module
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var EntityManager $entityManager */
        $entityManager = $serviceLocator->get(EntityManager::class);

        /* @var Entity\Module $moduleRepository */
        $moduleRepository = $entityManager->getRepository(Entity\Module::class);

        /* @var Client $githubClient */
        $githubClient = $serviceLocator->get('EdpGithub\Client');

        return new Module($moduleRepository, $githubClient);
    }
}
