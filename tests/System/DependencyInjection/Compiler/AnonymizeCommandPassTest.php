<?php

declare(strict_types=1);

namespace WebnetFr\DatabaseAnonymizerBundle\Tests\System\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use WebnetFr\DatabaseAnonymizerBundle\Command\AnonymizeCommand;
use WebnetFr\DatabaseAnonymizerBundle\Config\AnnotationConfigFactory;
use WebnetFr\DatabaseAnonymizerBundle\DependencyInjection\Compiler\AnonymizeCommandPass;

/**
 * @see    AnonymizeCommandPass
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class AnonymizeCommandPassTest extends AbstractCompilerPassTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AnonymizeCommandPass());
        $container->prependExtensionConfig('webnet_fr_database_anonymizer', $this->getConfig());
    }

    public function testConfigPassed(): void
    {
        $this->setDefinition(AnonymizeCommand::class, new Definition());
        $this->compile();

        $expectedConfig = [
            'connections' => [
                'default' => $this->getConfig(),
            ],
        ];

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            AnonymizeCommand::class,
            'setDefaultConfig',
            [$expectedConfig]
        );
    }

    public function testDoctrinePassed(): void
    {
        $this->setDefinition(AnonymizeCommand::class, new Definition());
        $this->setDefinition('doctrine', new Definition());
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            AnonymizeCommand::class,
            'setRegistry',
            [new Reference('doctrine')]
        );
    }

    public function testAnnotationReaderPassed(): void
    {
        $this->setDefinition(AnonymizeCommand::class, new Definition());
        $this->setDefinition('annotations.reader', new Definition());
        $this->compile();

        $this->assertContainerBuilderHasService(AnnotationConfigFactory::class);

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            AnonymizeCommand::class,
            'enableAnnotations',
            [new Reference(AnnotationConfigFactory::class)]
        );
    }

    /**
     * @return array
     */
    private function getConfig(): array
    {
        return [
            'defaults' => [
                'locale' => 'fr_FR',
            ],
            'tables'   => [
                'users' => [
                    'fields'      => [],
                    'primary_key' => [],
                    'truncate'    => false,
                ],
            ],
        ];
    }
}
