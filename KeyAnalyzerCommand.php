<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2017 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace Jose\Component\Console;

use Jose\Component\Core\Converter\JsonConverterInterface;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\KeyAnalyzer\JWKAnalyzerManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class KeyAnalyzerCommand.
 */
final class KeyAnalyzerCommand extends Command
{
    /**
     * @var JWKAnalyzerManager
     */
    private $analyzerManager;

    /**
     * @var JsonConverterInterface
     */
    private $jsonConverter;

    /**
     * KeyAnalyzerCommand constructor.
     *
     * @param JWKAnalyzerManager     $analyzerManager
     * @param JsonConverterInterface $jsonConverter
     * @param string|null            $name
     */
    public function __construct(JWKAnalyzerManager $analyzerManager, JsonConverterInterface $jsonConverter, string $name = null)
    {
        parent::__construct($name);
        $this->analyzerManager = $analyzerManager;
        $this->jsonConverter = $jsonConverter;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('key:analyze')
            ->setDescription('JWK quality analyzer.')
            ->setHelp('This command will analyze a JWK object and find security issues.')
            ->addArgument('jwk', InputArgument::REQUIRED, 'The JWK object')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jwk = $this->getKey($input);

        $result = $this->analyzerManager->analyze($jwk);
        foreach ($result as $message) {
            $output->writeln($message);
        }
    }

    /**
     * @param InputInterface $input
     *
     * @return JWK
     */
    private function getKey(InputInterface $input): JWK
    {
        $jwk = $input->getArgument('jwk');
        $json = $this->jsonConverter->decode($jwk);
        if (is_array($json)) {
            return JWK::create($json);
        }

        throw new \InvalidArgumentException('The argument must be a valid JWK.');
    }
}
