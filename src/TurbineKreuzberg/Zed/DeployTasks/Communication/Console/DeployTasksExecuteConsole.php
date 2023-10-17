<?php

namespace TurbineKreuzberg\Zed\DeployTasks\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig getConfig()
 * @method \TurbineKreuzberg\Zed\DeployTasks\Business\DeployTasksFacade getFacade()
 */
class DeployTasksExecuteConsole extends Console
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'deploy:tasks:execute';

    /**
     * @var string
     */
    public const DESCRIPTION = 'Execute all deploy tasks';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->getFacade()->executeTasks();

        return static::CODE_SUCCESS;
    }
}
