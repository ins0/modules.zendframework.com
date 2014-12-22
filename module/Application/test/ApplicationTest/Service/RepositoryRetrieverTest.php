<?php

namespace ApplicationTest\Service;

use Application\Service\RepositoryRetriever;
use EdpGithub\Api;
use EdpGithub\Listener\Exception\RuntimeException;
use PHPUnit_Framework_TestCase;

class RepositoryRetrieverTest extends PHPUnit_Framework_TestCase
{
    private function getClientMock(Api\AbstractApi $apiInstance, $result)
    {
        $headers = $this->getMock('Zend\Http\Headers');

        $response = $this->getMock('Zend\Http\Response');

        $response
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($result));

        $response->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue($headers));

        $httpClient = $this->getMock('EdpGithub\Http\Client');

        $httpClient
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue($response));

        $client = $this->getMock('EdpGithub\Client');

        $client->expects($this->any())
            ->method('getHttpClient')
            ->will($this->returnValue($httpClient));

        $apiInstance->setClient($client);

        $client
            ->expects($this->any())
            ->method('api')
            ->will($this->returnValue($apiInstance));

        return $client;
    }

    private function getRepositoryRetrieverInstance(Api\AbstractApi $apiInstance, $result)
    {
        return new RepositoryRetriever($this->getClientMock($apiInstance, $result));
    }

    public function testCanRetrieveUserRepositories()
    {
        $payload = [
            ['name' => 'foo'],
            ['name' => 'bar'],
            ['name' => 'baz']
        ];

        $instance = $this->getRepositoryRetrieverInstance(new Api\User, json_encode($payload));

        $repositories = $instance->getUserRepositories('foo');
        $this->assertInstanceOf('Generator', $repositories);

        $count = 0;
        foreach ($repositories as $repository) {
            $this->assertEquals(current($payload), (array)$repository);
            next($payload);
            ++$count;
        }

        $this->assertEquals(count($payload), $count);
    }

    public function testCanRetrieveUserRepositoryMetadata()
    {
        $payload = [
            'name' => 'foo',
            'url' => 'http://foo.com'
        ];

        $instance = $this->getRepositoryRetrieverInstance(new Api\Repos, json_encode($payload));
        $metadata = $instance->getUserRepositoryMetadata('foo', 'bar');

        $this->assertInstanceOf('stdClass', $metadata);
        $this->assertEquals($payload, (array)$metadata);
    }

    public function testErrorOnRetreiveUserRepositoryMetadata()
    {
        $client = $this->getMock('EdpGithub\Client');

        $client
            ->expects($this->once())
            ->method('api')
            ->willThrowException(new RuntimeException);

        $instance = new RepositoryRetriever($client);
        $response = $instance->getUserRepositoryMetadata('foo', 'bar');
        $this->assertFalse($response);
    }

    public function testCanRetrieveRepositoryFileContent()
    {
        $payload = [
            'content' => base64_encode('foo')
        ];
        $instance = $this->getRepositoryRetrieverInstance(new Api\Repos, json_encode($payload));
        $response = $instance->getRepositoryFileContent('foo', 'bar', 'foo.baz');

        $this->assertEquals('foo', $response);
    }

    public function testResponseContentMissingOnGetRepositoryFileContent()
    {
        $payload = [];
        $instance = $this->getRepositoryRetrieverInstance(new Api\Repos, json_encode($payload));
        $response = $instance->getRepositoryFileContent('foo', 'bar', 'baz');

        $this->assertFalse($response);
    }

    public function testCanRetrieveRepositoryFileMetadata()
    {
        $payload = [
            'name' => 'foo',
            'url' => 'http://foo.com'
        ];

        $instance = $this->getRepositoryRetrieverInstance(new Api\Repos, json_encode($payload));
        $metadata = $instance->getRepositoryFileMetadata('foo', 'bar', 'baz');

        $this->assertInstanceOf('stdClass', $metadata);
        $this->assertEquals($payload, (array)$metadata);
    }

    public function testErrorOnRetrieveRepositoryFileMetadata()
    {
        $client = $this->getMock('EdpGithub\Client');

        $client
            ->expects($this->once())
            ->method('api')
            ->willThrowException(new RuntimeException);

        $instance = new RepositoryRetriever($client);
        $response = $instance->getRepositoryFileMetadata('foo', 'bar', 'baz');
        $this->assertFalse($response);
    }

    public function testCanRetrieveAuthenticatedUserRepositories()
    {
        $payload = [
            ['name' => 'foo'],
            ['name' => 'bar'],
            ['name' => 'baz']
        ];

        $instance = $this->getRepositoryRetrieverInstance(new Api\CurrentUser, json_encode($payload));

        $repositories = $instance->getAuthenticatedUserRepositories();
        $this->assertInstanceOf('Generator', $repositories);

        $count = 0;
        foreach ($repositories as $repository) {
            $this->assertEquals(current($payload), (array)$repository);
            next($payload);
            ++$count;
        }

        $this->assertEquals(count($payload), $count);
    }
}
