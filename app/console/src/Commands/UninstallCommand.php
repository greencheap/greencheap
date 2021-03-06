<?php

namespace GreenCheap\Console\Commands;

use GreenCheap\Application\Console\Command;
use GreenCheap\Installer\Package\PackageManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UninstallCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = "uninstall";

    /**
     * {@inheritdoc}
     */
    protected $description = "Uninstalls a GreenCheap package";

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->addArgument("packages", InputArgument::IS_ARRAY | InputArgument::REQUIRED, "[Package name]");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $updater = new PackageManager($output);
        $updater->uninstall((array) $this->argument("packages"));

        return 0;
    }
}
