<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfModule\Service;

class TotalModules extends AbstractHelper
{
    /**
     * @var Service\Module
     */
    private $moduleService;

    /**
     * @var int
     */
    private $total;

    /**
     * @param Service\Module $moduleService
     */
    public function __construct(Service\Module $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * @return int
     */
    public function __invoke()
    {
        if ($this->total === null) {
            $this->total = $this->moduleService->getTotalModuleCount();
        }

        return $this->total;
    }
}
