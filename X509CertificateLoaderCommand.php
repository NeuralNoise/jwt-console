<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace Jose\Component\Console;

use Jose\Component\KeyManagement\JWKFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class X509CertificateLoaderCommand extends GeneratorCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('key:load:x509')
            ->setDescription('Load a key from a X.509 certificate file.')
            ->addArgument('file', InputArgument::REQUIRED, 'Filename of the X.509 certificate.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('file');
        $args = [];
        foreach (['use', 'alg'] as $key) {
            $value = $input->getOption($key);
            if (null !== $value) {
                $args[$key] = $value;
            }
        }

        $jwk = JWKFactory::createFromCertificateFile($filename, $args);
        $this->prepareJsonOutput($input, $output, $jwk);
    }
}
