<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Factory;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Dot\DataFixtures\Command\ExecuteFixturesCommand;
use Dot\DataFixtures\Exception\NotFoundException;
use Dot\DataFixtures\Factory\ExecuteFixturesCommandFactory;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function getcwd;

class ExecuteFixturesCommandFactoryTest extends TestCase
{
    protected ContainerInterface|MockObject $container;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateServiceWithoutEntityManager(): void
    {
        $this->container->expects($this->once())
            ->method('has')
            ->with(EntityManager::class)
            ->willReturn(false);
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('EntityManager not found.');
        (new ExecuteFixturesCommandFactory())($this->container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateServiceWithoutPath(): void
    {
        $this->container->method('has')->willReturnMap([
            [EntityManager::class, true],
            ['config', true],
        ]);
        $this->container->method('get')
            ->with('config')
            ->willReturn(null);
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Key `fixtures` not found in doctrine configuration.');
        (new ExecuteFixturesCommandFactory())($this->container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function testPath(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $connection    = $this->createMock(Connection::class);
        $entityManager = $this->createMock(EntityManager::class);
        $eventManager  = $this->createMock(EventManager::class);
        $connection->method('getConfiguration')->willReturn($configuration);
        $entityManager->method('getConnection')->willReturn($connection);
        $entityManager->method('getEventManager')->willReturn($eventManager);

        $this->container->method('has')->willReturnMap([
            [EntityManager::class, true],
            ['config', true],
        ]);

        $this->container->method('get')->willReturnMap([
            [EntityManager::class, $entityManager],
            ['config', ['doctrine' => ['fixtures' => getcwd() . '/data/doctrine/fixtures']]],
        ]);

        $factory = (new ExecuteFixturesCommandFactory())($this->container);
        $this->assertInstanceOf(ExecuteFixturesCommand::class, $factory);
        $path = $this->container->get('config')['doctrine']['fixtures'];
        $this->assertSame(getcwd() . '/data/doctrine/fixtures', $path);
    }
}
