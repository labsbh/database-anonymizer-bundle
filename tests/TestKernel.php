<?php

declare(strict_types=1);

namespace WebnetFr\DatabaseAnonymizerBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(
            static function (ContainerBuilder $container) {
                $container->loadFromExtension(
                    'doctrine', [
                    'dbal' => [
                        'driver'                => $GLOBALS['db_type'],
                        'charset'               => 'utf8mb4',
                        'default_table_options' => [
                            'charset' => 'utf8mb4',
                            'collate' => 'utf8mb4_unicode_ci',
                        ],
                        'dbname'                => $GLOBALS['db_name'],
                        'host'                  => $GLOBALS['db_host'],
                        'port'                  => $GLOBALS['db_port'],
                        'user'                  => $GLOBALS['db_username'],
                        'password'              => $GLOBALS['db_password'],
                    ],
                    'orm'  => [
                        'auto_generate_proxy_classes' => true,
                        'naming_strategy'             => 'doctrine.orm.naming_strategy.underscore_number_aware',
                        'auto_mapping'                => true,
                        'mappings'                    => [
                            'Entity' => [
                                'is_bundle' => false,
                                'type'      => 'annotation',
                                'dir'       => '%kernel.project_dir%/tests/Entity',
                                'prefix'    => 'WebnetFr\DatabaseAnonymizerBundle\Tests\Entity',
                                'alias'     => 'Entity',
                            ],
                        ],
                    ],
                ]);
            });
    }

}
