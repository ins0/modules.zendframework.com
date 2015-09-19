<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfModule\Service;

class SearchController extends AbstractActionController
{
    /**
     * @var Service\Module
     */
    private $moduleService;

    /**
     * @param Service\Module $moduleService
     */
    public function __construct(Service\Module $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    public function indexAction()
    {
        $query =  $this->params()->fromQuery('query', null);

        $results = $this->moduleService->findModules($query, ['m.name']);

        $viewModel = new ViewModel([
            'results' => $results,
        ]);
        $viewModel->setTerminal(true);

        return $viewModel;
    }
}
