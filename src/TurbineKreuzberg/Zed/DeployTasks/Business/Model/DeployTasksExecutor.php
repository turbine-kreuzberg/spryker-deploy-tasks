<?php

namespace TurbineKreuzberg\Zed\DeployTasks\Business\Model;

use Symfony\Component\Process\Process;
use TurbineKreuzberg\Shared\DeployTasks\DeployTasksConstants;
use TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig;

class DeployTasksExecutor
{
    /**
     * @var \TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig
     */
    private DeployTasksConfig $config;

    /**
     * @var \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksLogger
     */
    private DeployTasksLogger $tasksLogger;

    /**
     * @param \TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig $config
     * @param \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksLogger $tasksLogger
     */
    public function __construct(
        DeployTasksConfig $config,
        DeployTasksLogger $tasksLogger
    ) {
        $this->config = $config;
        $this->tasksLogger = $tasksLogger;
    }

    /**
     * @param array<string, string|array<string>> $task
     *
     * @return bool
     */
    public function execute(array $task): bool
    {
        if ($this->isExecutionSkippedForCurrentStore($task)) {
            return true;
        }

        if ($this->isExecutionSkippedInCurrentEnvironment($task)) {
            return true;
        }

        /**
         * @var string $command
         */
        $command = $task[DeployTasksConstants::YAML_KEY_COMMAND];

        $this->tasksLogger->writeOutput(sprintf('Execute: %s', $command));

        if (
            isset($task[DeployTasksConstants::YAML_KEY_DESCRIPTION])
            && is_string($task[DeployTasksConstants::YAML_KEY_DESCRIPTION])
        ) {
            $this->tasksLogger->writeOutput(
                sprintf(
                    'Description: %s',
                    $task[DeployTasksConstants::YAML_KEY_DESCRIPTION],
                ),
            );
        }

        $process = $this->createProcess($command);
        $process->run();
        $output = $process->getOutput();

        if ($output) {
            $this->tasksLogger->writeOutput('Output:');
            $this->tasksLogger->writeOutput($output, false);
        }

        if ($process->getExitCode() !== 0) {
            $this->tasksLogger->writeOutput(
                sprintf(
                    '[DEPLOY-TASK-ERROR] Task failed with exit code %s, error output: %s',
                    $process->getExitCode(),
                    $process->getErrorOutput(),
                ),
            );

            return false;
        }

        return true;
    }

    /**
     * @param array<string, string|array<string>> $task
     *
     * @return bool
     */
    private function isExecutionSkippedForCurrentStore(array $task): bool
    {
        /**
         * @var array<string> $storesToExecuteFor
         */
        $storesToExecuteFor = $task[DeployTasksConstants::YAML_KEY_EXECUTE_FOR_STORE];
        $currentStore = getenv('APPLICATION_STORE');

        if (in_array($currentStore, $storesToExecuteFor, true)) {
            return false;
        }

        /**
         * @var string $command
         */
        $command = $task[DeployTasksConstants::YAML_KEY_COMMAND];

        $this->tasksLogger->writeOutput(
            sprintf(
                "Command '%s' will be skipped for store '%s'",
                $command,
                $currentStore,
            ),
        );

        return true;
    }

    /**
     * @param array<string, string|array<string>> $task
     *
     * @return bool
     */
    private function isExecutionSkippedInCurrentEnvironment(array $task): bool
    {
        /**
         * @var array<string> $environmentsToExecuteOn
         */
        $environmentsToExecuteOn = $task[DeployTasksConstants::YAML_KEY_EXECUTE_ON];
        $currentEnvironment = getenv('SHOP_ENV');

        if (in_array($currentEnvironment, $environmentsToExecuteOn, true)) {
            return false;
        }

        /**
         * @var string $command
         */
        $command = $task[DeployTasksConstants::YAML_KEY_COMMAND];

        $this->tasksLogger->writeOutput(
            sprintf(
                "Command '%s' will be skipped in env '%s'",
                $command,
                $currentEnvironment,
            ),
        );

        return true;
    }

    /**
     * @param string $command
     *
     * @return \Symfony\Component\Process\Process
     */
    private function createProcess(string $command): Process
    {
        return Process::fromShellCommandline(
            $command,
            APPLICATION_ROOT_DIR,
            getenv(),
            null,
            $this->config->getProcessTimeout(),
        );
    }
}
