<?php

namespace GreenCheap\Console\Commands;

use GreenCheap\Application\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TranslationFetchCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'translation:fetch';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Fetches current translation files from languages repository';

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output):void
    {
        parent::initialize($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $tmp  = '/tmp/greencheap-languages';
        $repo = 'git@github.com:greencheap/languages.git';

        // if cloned repo exists? rm
        if(file_exists($tmp)) {
            exec(sprintf('rm -rf %s', $tmp));
        }

        // git clone to tmp
        exec(sprintf("git clone %s %s", $repo, $tmp));

        // foreach resource:
        //$resources = ['system', 'blog', 'theme-one'];
        $resources = ['system'];

        // mv translation files to correct folder
        foreach ($resources as $resource) {
            $from = sprintf("%s/%s/*", $tmp, $resource);

            if($to = $this->getPath($resource)) {

                $this->info("[${resource}] Moving languages files to: ".$to);
                exec(sprintf('rsync -av %s %s', $from, $to));

            } else {

                $this->error("[$resource] Package not found. Skipping.");

            }
        }

        // rm git repo from tmp
        exec(sprintf('rm -rf %s', $tmp));

        return 0;
    }


    /**
     * Returns the extension path.
     *
     * @param $resource
     * @return string|boolean
     */
    protected function getPath($resource):string|bool
    {
        $vendor = 'greencheap';

        if ($resource == "system") {
            $path = sprintf('%s/app/system', $this->container['path']);
        } else {
            $path = sprintf('%s/%s/%s',
                $this->container['path.packages'],
                $vendor,
                $resource);
        }

        if (!is_dir($path)) {
            return false;
        }

        return $path;
    }
}
