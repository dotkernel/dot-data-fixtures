<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Fixtures;

use Composer\InstalledVersions;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Tools\SchemaTool;

use function version_compare;

use const PHP_VERSION_ID;

/**
 * Builds an in-memory SQLite entity manager with the `role` table created.
 */
final class EntityManagerFactory
{
    public const FIXTURES_PATH = __DIR__ . '/Data';

    public static function create(): EntityManager
    {
        $config = new Configuration();
        $config->setMetadataDriverImpl(new AttributeDriver([__DIR__ . '/Entity']));
        // ORM 3.5+ on PHP 8.4+ needs native lazy objects; older ORM 3.x uses symfony/var-exporter proxies instead.
        $ormVersion = InstalledVersions::getVersion('doctrine/orm') ?? '0';
        if (PHP_VERSION_ID >= 80400 && version_compare($ormVersion, '3.5.0', '>=')) {
            $config->enableNativeLazyObjects(true);
        }

        $connection    = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $config);
        $entityManager = new EntityManager($connection, $config);
        (new SchemaTool($entityManager))->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        return $entityManager;
    }

    /**
     * @return list<mixed>
     */
    public static function roleNames(EntityManager $entityManager): array
    {
        return $entityManager->getConnection()->fetchFirstColumn('SELECT name FROM role ORDER BY id');
    }
}
