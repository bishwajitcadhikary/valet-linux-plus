<?php

namespace Valet;

use DomainException;
use Valet\Contracts\PackageManager;
use Valet\Contracts\ServiceManager;

class Mailpit
{
    public $pm;
    public $sm;
    public $cli;
    public $files;

    /**
     * Create a new Mailpit instance.
     *
     * @param PackageManager $pm
     * @param ServiceManager $sm
     * @param CommandLine    $cli
     * @param Filesystem     $files
     *
     * @return void
     */
    public function __construct(PackageManager $pm, ServiceManager $sm, CommandLine $cli, Filesystem $files)
    {
        $this->cli = $cli;
        $this->pm = $pm;
        $this->sm = $sm;
        $this->files = $files;
    }

    /**
     * Install the configuration files for Mailpit.
     *
     * @return void
     */
    public function install()
    {
        $this->ensureInstalled();

        $this->createService();

        $this->sm->start('mailpit');
    }

    /**
     * Validate if system already has Mailpit installed in it.
     *
     * @return void
     */
    public function ensureInstalled()
    {
        if (!$this->isAvailable()) {
            $this->cli->run('ln -s '.VALET_BIN_PATH.'/mailpit /opt/valet-linux/mailpit');
        }
    }

    /**
     * @return void
     */
    public function createService()
    {
        info('Installing Mailpit service...');

        $servicePath = '/etc/init.d/mailpit';
        $serviceFile = VALET_ROOT_PATH.'/cli/stubs/init/mailpit.sh';
        $hasSystemd = $this->sm->_hasSystemd();

        if ($hasSystemd) {
            $servicePath = '/etc/systemd/system/mailpit.service';
            $serviceFile = VALET_ROOT_PATH.'/cli/stubs/init/mailpit';
        }

        $this->files->put(
            $servicePath,
            $this->files->get($serviceFile)
        );

        if (!$hasSystemd) {
            $this->cli->run("chmod +x $servicePath");
        }

        $this->sm->enable('mailpit');

        $this->updateDomain();

        \Nginx::restart();
    }

    /**
     * Update domain for HTTP access.
     *
     * @return void
     */
    public function updateDomain()
    {
        $domain = \Configuration::read()['domain'];

        \Site::secure("mailpit.{$domain}", __DIR__.'/../stubs/mailpit.conf');
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        try {
            $output = $this->cli->run(
                'which mailpit',
                function () {
                    throw new DomainException('Service not available');
                }
            );

            return $output != '';
        } catch (DomainException $e) {
            return false;
        }
    }

    /**
     * Start the Mailpit service.
     *
     * @return void
     */
    public function start()
    {
        $this->sm->start('mailpit');
    }

    /**
     * Restart the Mailpit service.
     *
     * @return void
     */
    public function restart()
    {
        $this->sm->restart('mailpit');
    }

    /**
     * Stop the Mailpit service.
     *
     * @return void
     */
    public function stop()
    {
        $this->sm->stop('mailpit');
    }

    /**
     * Mailpit service status.
     *
     * @return void
     */
    public function status()
    {
        $this->sm->printStatus('mailpit');
    }

    /**
     * Prepare Mailpit for uninstall.
     *
     * @return void
     */
    public function uninstall()
    {
        $this->stop();
    }
}
