<?php

namespace ZfModule\Mapper;

use Zend\Feed\Writer\Entry;
use Zend\Feed\Writer\Feed;
use Zend\Mvc\Controller\Plugin\Url as UrlPlugin;
use ZfModule\Entity\Module as ModuleEntity;

/**
 * ModuleToFeed
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class ModuleToFeed
{
    /**
     * @var Feed
     */
    protected $feed;

    /**
     * @var UrlPlugin
     */
    protected $urlPlugin;

    /**
     * @param Feed $feed
     */
    public function __construct(Feed $feed, UrlPlugin $urlPlugin)
    {
        $this->feed = $feed;
        $this->urlPlugin = $urlPlugin;
    }

    /**
     * @param array $modules
     */
    public function addModules($modules)
    {
        foreach ($modules as $module) {
            $this->addModule($module);
        }
    }

    /**
     * @param ModuleEntity $module
     * @return Entry
     */
    public function addModule(ModuleEntity $module)
    {
        $moduleDescription = $module->getDescription();

        if (empty($moduleDescription)) {
            $moduleDescription = 'No description available';
        }

        $moduleName = $module->getName();
        $urlParams = ['vendor' => $module->getOwner(), 'module' => $moduleName];

        $entry = $this->feed->createEntry();

        $entry->setId($module->getIdentifier());
        $entry->setTitle($moduleName);
        $entry->setDescription($moduleDescription);
        $entry->setLink($this->urlPlugin->fromRoute('view-module', $urlParams, ['force_canonical' => true]));
        $entry->addAuthor(['name' => $module->getOwner()]);
        $entry->setDateCreated($module->getCreatedAt());

        $this->feed->addEntry($entry);

        return $entry;
    }
}
