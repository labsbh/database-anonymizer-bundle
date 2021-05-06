<?php

declare(strict_types=1);

namespace WebnetFr\DatabaseAnonymizerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WebnetFr\DatabaseAnonymizer\DependencyInjection\Compiler\ChainGeneratorFactoryPass;
use WebnetFr\DatabaseAnonymizerBundle\DependencyInjection\Compiler\AnonymizeCommandPass;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class WebnetFrDatabaseAnonymizerBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AnonymizeCommandPass());
        $container->addCompilerPass(new ChainGeneratorFactoryPass());
    }
}
