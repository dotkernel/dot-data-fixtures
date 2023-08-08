<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Factory;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Dot\DataFixtures\Command\ListFixturesCommand;
use Dot\DataFixtures\Exception\NotFoundException;
use Dot\DataFixtures\Factory\ListFixturesCommandFactory;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function getcwd;

class ListFixturesCommandFactoryTest extends TestCase
{
    private ContainerInterface|MockObject $container;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     */
    public function testWithoutConfig(): void
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn(null);
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Key `fixtures` not found in doctrine configuration.');
        $factory = (new ListFixturesCommandFactory())($this->container);
        $this->assertInstanceOf(ListFixturesCommand::class, $factory);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testPathWithConfig(): void
    {
        $entityManager = $this->createMock(EntityManager::class);
        $loader        = $this->createMock(Loader::class);
        $purger        = $this->createMock(ORMPurger::class);
        $executor      = $this->createMock(ORMExecutor::class);

        $this->container->method('get')->willReturnMap([
            [EntityManager::class, $entityManager],
            [Loader::class, $loader],
            [ORMPurger::class, $purger],
            [ORMExecutor::class, $executor],
            ['config', ['doctrine' => ['fixtures' => getcwd() . '/data/doctrine/fixtures']]],
        ]);
        $factory = (new ListFixturesCommandFactory())($this->container);
        $this->assertInstanceOf(ListFixturesCommand::class, $factory);
        $path = $this->container->get('config')['doctrine']['fixtures'];
        $this->assertSame(getcwd() . '/data/doctrine/fixtures', $path);
    }
}
