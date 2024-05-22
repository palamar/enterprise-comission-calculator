<?php

declare(strict_types=1);

namespace CommissionCalculator;

use Symfony\Component\Console\Application as BaseApplication;

class App extends BaseApplication
{
    public function __construct(iterable $commands = [])
    {
        $commands = $commands instanceof \Traversable ? \iterator_to_array($commands) : $commands;

        foreach ($commands as $command) {
            $this->add($command);
        }

        parent::__construct('Commission Calculator', '1.0.0');
    }
}
