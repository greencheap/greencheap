<?php

namespace GreenCheap\Console\Commands;

use GreenCheap\Application as App;
use GreenCheap\Application\Console\Command;
use GreenCheap\Installer\Helper\Composer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class BuildCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'build';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Builds a .zip release file';

    /**
     * @var string[]
     */
    protected $excludes = [
        '^(tmp|config\.php|greencheap.+\.zip|greencheap.db|.+\.map)',
        '^app\/assets\/[^\/]+\/(dist\/vue-.+\.js|dist\/jquery\.js|lodash\.js)',
        '^app\/assets\/(jquery|vue)\/(src|perf|external)',
        '^app\/vendor\/lusitanian\/oauth\/examples',
        '^app\/vendor\/maximebf\/debugbar\/src\/DebugBar\/Resources',
        '^app\/vendor\/nickic\/php-parser\/(grammar|test_old)',
        '^app\/vendor\/(phpdocumentor|phpspec|phpunit|sebastian|symfony\/yaml)',
        '^app\/vendor\/[^\/]+\/[^\/]+\/(build|docs?|tests?|changelog|phpunit|upgrade?)',
        'node_modules'
    ];

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->container->path();
        $version = $this->container->version();

        $filter = '/' . implode('|', $this->excludes) . '/i';
        $packages = [
            'greencheap/blog' => '*',
            'greencheap/theme-one' => '*',
            'greencheap/seo' => '*'
        ];

        $config = [];
        foreach (['path.temp', 'path.cache', 'path.vendor', 'path.artifact', 'path.packages', 'system.api'] as $key) {
            $config[$key] = $this->container->get($key);
        }

        $composer = new Composer($config, $output);
        $composer->install($packages);

        $this->line(sprintf('Starting: webpack'));

        exec('yarn build --mode=production');

        $this->line(sprintf('Building Package.'));

        $finder = Finder::create()->files()->in($path)->ignoreVCS(true)->filter(function ($file) use ($filter) {
            return !preg_match($filter, $file->getRelativePathname());
        });

        $zip = new \ZipArchive;

        if (true !== $zip->open($zipFile = "{$path}/greencheap-{$version}.zip", \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            $this->abort("Can't open ZIP extension in '{$zipFile}'");
        }

        foreach ($finder as $file) {
            $zip->addFile($file->getPathname(), $file->getRelativePathname());
        }

        $zip->addFile("{$path}/.bowerrc", '.bowerrc');
        $zip->addFile("{$path}/.htaccess", '.htaccess');

        $zip->addEmptyDir('tmp/');
        $zip->addEmptyDir('tmp/cache');
        $zip->addEmptyDir('tmp/temp');
        $zip->addEmptyDir('tmp/logs');
        $zip->addEmptyDir('tmp/sessions');
        $zip->addEmptyDir('tmp/packages');

        $zip->close();

        $name = basename($zipFile);
        $size = filesize($zipFile) / 1024 / 1024;

        $this->line(sprintf('Build: %s (%.2f MB)', $name, $size));
        return 0;
    }
}
