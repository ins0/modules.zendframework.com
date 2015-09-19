<?php

namespace ZfModule\Service;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use EdpGithub\Client;
use EdpGithub\Collection\RepositoryCollection;
use EdpGithub\Http\Client as HttpClient;
use stdClass;
use Zend\Http;
use Zend\Paginator;
use ZfcBase\EventManager\EventProvider;
use ZfModule\Entity;
use ZfModule\Repository;

class Module extends EventProvider
{
    /**
     * @var Repository\Module
     */
    private $moduleRepository;

    /**
     * @var Client
     */
    private $githubClient;

    /**
     * @param Repository\Module $moduleRepository
     * @param Client $githubClient
     */
    public function __construct(Repository\Module $moduleRepository, Client $githubClient)
    {
        $this->moduleRepository = $moduleRepository;
        $this->githubClient = $githubClient;
    }

    /**
     * Return Total Modules
     * @return int
     */
    public function getTotalModuleCount()
    {
        return $this->moduleRepository->countTotalModules()->getSingleScalarResult();
    }

    /**
     * Get all Modules
     *
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getModules($limit = null, $offset = null)
    {
        return $this->moduleRepository->findAll($limit, $offset)->getResult();
    }

    /**
     * Find Modules
     *
     * @param $searchTerm
     * @param $orderBy
     * @param int $currentPage
     * @param null $perPage
     * @return array|Paginator\Paginator
     */
    public function findModules($searchTerm, $orderBy, $currentPage = 1, $perPage = null)
    {
        $modules = $this->moduleRepository->findModulesByName($searchTerm, $orderBy);

        if ($perPage) {
            $paginatorAdapter = new DoctrinePaginator(new ORMPaginator($modules));
            $paginator = new Paginator\Paginator($paginatorAdapter);
            $paginator->setItemCountPerPage($perPage);
            $paginator->setCurrentPageNumber($currentPage);

            return $paginator;
        }

        return $modules->getResult();
    }

    /**
     * @param stdClass $repository
     * @return Entity\Module
     */
    public function register($repository)
    {
        $isUpdate = false;

        $module = $this->moduleMapper->findByUrl($repository->html_url);

        if ($module) {
            $isUpdate = true;
        } else {
            $module  = new Entity\Module();
        }

        $module->setName($repository->name);
        $module->setDescription($repository->description);
        $module->setUrl($repository->html_url);
        $module->setOwner($repository->owner->login);
        $module->setPhotoUrl($repository->owner->avatar_url);

        if ($isUpdate) {
            $this->moduleMapper->update($module);
        } else {
            $this->moduleMapper->insert($module);
        }

        return $module;
    }

    /**
     * Check if Repo is a ZF Module
     *
     * @param stdClass $repository
     * @return bool
     */
    public function isModule(stdClass $repository)
    {
        $query = sprintf(
            'repo:%s/%s filename:Module.php "class Module"',
            $repository->owner->login,
            $repository->name
        );

        $path = sprintf(
            'search/code?q=%s',
            $query
        );

        /* @var HttpClient $httpClient */
        $httpClient = $this->githubClient->getHttpClient();

        /* @var Http\Response $response */
        $response = $httpClient->request($path);

        $result = json_decode($response->getBody(), true);

        if (isset($result['total_count']) && $result['total_count'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $limit
     * @return Entity\Module[]
     */
    public function allModules($limit = null)
    {
        return $this->moduleMapper->findAll(
            $limit,
            'created_at',
            'DESC'
        );
    }

    /**
     * @return stdClass[]
     */
    public function currentUserModules()
    {
        /* @var RepositoryCollection $repositoryCollection */
        $repositoryCollection = $this->githubClient->api('current_user')->repos([
            'type' => 'all',
            'per_page' => 100,
        ]);

        return array_filter(iterator_to_array($repositoryCollection), function ($repository) {
            if (true === $repository->fork) {
                return false;
            }

            if (false === $repository->permissions->push) {
                return false;
            }

            if (!$this->moduleMapper->findByUrl($repository->html_url)) {
                return false;
            }

            return true;
        });
    }
}
