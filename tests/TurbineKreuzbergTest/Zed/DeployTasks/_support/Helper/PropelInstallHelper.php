<?php

namespace TurbineKreuzbergTest\Zed\DeployTasks\Helper;

use Codeception\Configuration;
use Codeception\Module;
use Spryker\Zed\Propel\Business\PropelFacade;
use Symfony\Component\Process\Process;

class PropelInstallHelper extends Module
{
    /**
     * @var string
     */
    protected const DEFAULT_STORE = 'DE';

    /**
     * @return void
     */
    public function _initialize(): void
    {
        if (empty($this->config['enabled'])) {
            return;
        }

        $this->initPropel();
    }

    /**
     * @return void
     */
    protected function initPropel(): void
    {
        $this->getFacade()->deleteMigrationFilesDirectory();
        $this->getFacade()->cleanPropelSchemaDirectory();
        $this->getFacade()->copySchemaFilesToTargetDirectory();
        $this->getFacade()->createDatabase();

        $this->runCommands();
    }

    /**
     * @return void
     */
    private function runCommands(): void
    {
        foreach ($this->getCommands() as $command) {
            $this->runCommand($command);
        }
    }

    /**
     * @return array
     */
    private function getCommands(): array
    {
        return [
            $this->createDiffCommand(),
            $this->createMigrateCommand(),
            $this->getModelBuildCommand(),
        ];
    }

    /**
     * @return string
     */
    private function getModelBuildCommand(): string
    {
        return $this->getBaseCommand() . ' ' . APPLICATION_ROOT_DIR . 'bin/console propel:model:build';
    }

    /**
     * @return string
     */
    private function getBaseCommand(): string
    {
        return 'APPLICATION_ENV=' . APPLICATION_ENV
            . ' APPLICATION_STORE=' . $this->getApplicationStore()
            . ' APPLICATION_ROOT_DIR=' . APPLICATION_ROOT_DIR
            . ' APPLICATION=' . APPLICATION;
    }

    /**
     * @return string
     */
    private function createDiffCommand(): string
    {
        return $this->getBaseCommand() . ' ' . APPLICATION_ROOT_DIR . 'bin/console propel:diff -vv';
    }

    /**
     * @return string
     */
    private function createMigrateCommand(): string
    {
        return $this->getBaseCommand() . ' ' . APPLICATION_ROOT_DIR . 'bin/console propel:migrate -vv';
    }

    /**
     * @param string $command
     *
     * @return void
     */
    protected function runCommand(string $command): void
    {
        $process = $this->createProcess($command);
        $process->setTimeout(600);
        $process->mustRun(
            function ($type, $buffer) use ($command): void {
                if ($type === Process::ERR) {
                    echo $command . ' Failed:' . PHP_EOL;
                    echo $buffer;
                }
            },
        );
    }

    /**
     * @return \Spryker\Zed\Propel\Business\PropelFacade
     */
    private function getFacade(): PropelFacade
    {
        return new PropelFacade();
    }

    /**
     * @param string $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function createProcess(string $command): Process
    {
        return Process::fromShellCommandline($command, Configuration::projectDir());
    }

    /**
     * @return string
     */
    private function getApplicationStore(): string
    {
        if (!defined('APPLICATION_STORE')) {
            return static::DEFAULT_STORE;
        }

        return APPLICATION_STORE;
    }
}
