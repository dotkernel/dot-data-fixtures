<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Fixtures;

use Composer\InstalledVersions;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Tools\SchemaTool;

use function sys_get_temp_dir;
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
        // ORM 3.5+ on PHP 8.4+ needs native lazy objects; otherwise ORM generates proxy classes,
        // and before ORM 3.5 the ProxyFactory requires a proxy directory and namespace (as ORMSetup sets them).
        $ormVersion = InstalledVersions::getVersion('doctrine/orm') ?? '0';
        if (PHP_VERSION_ID >= 80400 && version_compare($ormVersion, '3.5.0', '>=')) {
            $config->enableNativeLazyObjects(true);
        } else {
            $config->setProxyDir(sys_get_temp_dir());
            $config->setProxyNamespace('DotTestDataFixturesProxies');
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
