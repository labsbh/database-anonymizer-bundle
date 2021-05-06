<?php

declare(strict_types=1);

namespace WebnetFr\DatabaseAnonymizerBundle\Tests\System\Command;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use WebnetFr\DatabaseAnonymizer\Anonymizer;
use WebnetFr\DatabaseAnonymizer\ConfigGuesser\ConfigGuesser;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\ChainGeneratorFactory;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\ConstantGeneratorFactory;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\FakerGeneratorFactory;
use WebnetFr\DatabaseAnonymizerBundle\Command\AnonymizeCommand;
use WebnetFr\DatabaseAnonymizerBundle\Config\AnnotationConfigFactory;
use WebnetFr\DatabaseAnonymizerBundle\Tests\System\SystemTestTrait;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class AnonymizeCommandTest extends KernelTestCase
{
    use SystemTestTrait;

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->regenerateUsersOrders();
    }

    public function testWithConfigFile(): void
    {
        $generator = new ChainGeneratorFactory();
        $generator->addFactory(new ConstantGeneratorFactory())
                  ->addFactory(new FakerGeneratorFactory());

        $command = (new Application('Database anonymizer', '0.0.1'))
            ->add(new AnonymizeCommand($generator, new Anonymizer()));

        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['y']);
        $commandTester->execute(
            [
                'command'    => $command->getName(),
                '--config'   => realpath(__DIR__.'/../../config/config.yaml'),
                '--type'     => $GLOBALS['db_type'],
                '--host'     => $GLOBALS['db_host'],
                '--port'     => $GLOBALS['db_port'],
                '--database' => $GLOBALS['db_name'],
                '--user'     => $GLOBALS['db_username'],
                '--password' => $GLOBALS['db_password'],
            ]);

        $this->doTestValues();
    }

    public function testWithAnnotations(): void
    {
        self::bootKernel();
        $this->getConnection();
        $generator  = new ChainGeneratorFactory();
        $generator->addFactory(new ConstantGeneratorFactory())
                  ->addFactory(new FakerGeneratorFactory());

        $annotationReader        = new AnnotationReader();
        $configGuesser           = new ConfigGuesser();
        $annotationConfigFactory = new AnnotationConfigFactory($annotationReader, $configGuesser);
        $anonymizeCommand        = new AnonymizeCommand($generator, new Anonymizer());
        $anonymizeCommand->enableAnnotations($annotationConfigFactory);

        /** @var ManagerRegistry $registry */
        $registry = self::$container->get('doctrine');
        $anonymizeCommand->setRegistry($registry);

        $command = (new Application('Database anonymizer', '0.0.1'))
            ->add($anonymizeCommand);

        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['y']);
        $commandTester->execute(
            [
                'command'       => $command->getName(),
                '--annotations' => true,
                '--em'          => 'default',
            ]);

        $this->doTestValues();
    }

    /**
     * Test actual values.
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    private function doTestValues(): void
    {
        $connection = $this->getConnection();

        $selectSQL  = $connection
            ->createQueryBuilder()
            ->select('email, firstname, lastname, birthdate, phone, password')
            ->from('users')
            ->getSQL();
        $selectStmt = $connection->prepare($selectSQL);
        $result     = $selectStmt->executeQuery();

        while ($row = $result->fetchAssociative()) {
            self::assertIsString($row['email']);
            self::assertIsString($row['firstname']);
            self::assertIsString($row['lastname']);
            self::assertIsString($row['birthdate']);
            self::assertTrue(is_string($row['phone']) || is_null($row['phone']));
            self::assertIsString($row['password']);
        }

        $selectSQL  = $connection
            ->createQueryBuilder()
                                 ->select('address, street_address, zip_code, city, country, comment, comment, created_at')
                                 ->from('orders')
                                 ->getSQL();
        $selectStmt = $connection->prepare($selectSQL);
        $result     = $selectStmt->executeQuery();

        while ($row = $result->fetchAssociative()) {
            self::assertIsString($row['address']);
            self::assertIsString($row['street_address']);
            self::assertIsString($row['zip_code']);
            self::assertIsString($row['city']);
            self::assertIsString($row['country']);
            self::assertIsString($row['comment']);
            self::assertIsString($row['created_at']);
        }
    }
}
