<?php

namespace Application\Controller;

use Zend\Feed\Writer\Feed;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator;
use Zend\View\Model\FeedModel;
use Zend\View\Model\ViewModel;
use ZfModule\Mapper\ModuleToFeed;
use ZfModule\Service;

class IndexController extends AbstractActionController
{
    const MODULES_PER_PAGE = 15;

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

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $query =  $this->params()->fromQuery('query', null);
        $page = (int) $this->params()->fromQuery('page', 1);

        $repositories = $this->moduleService->findModules($query, ['m.created_at DESC'], $page, self::MODULES_PER_PAGE);

        return new ViewModel([
            'repositories' => $repositories,
            'query' => $query,
        ]);
    }

    /**
     * RSS feed for recently added modules
     * @return FeedModel
     */
    public function feedAction()
    {
        $url = $this->plugin('url');
        // Prepare the feed
        $feed = new Feed();
        $feed->setTitle('ZF2 Modules');
        $feed->setDescription('Recently added ZF2 modules');
        $feed->setFeedLink($url->fromRoute('feed', [], ['force_canonical' => true]), 'atom');
        $feed->setLink($url->fromRoute('home', [], ['force_canonical' => true]));

        // Get the recent modules
        $page = 1;
        $modules = $this->moduleService->findModules(null, ['m.created_at DESC'], $page, self::MODULES_PER_PAGE);

        // Load them into the feed
        $mapper = new ModuleToFeed($feed, $url);
        $mapper->addModules($modules);

        // Render the feed
        $feedmodel = new FeedModel();
        $feedmodel->setFeed($feed);

        return $feedmodel;
    }
}
