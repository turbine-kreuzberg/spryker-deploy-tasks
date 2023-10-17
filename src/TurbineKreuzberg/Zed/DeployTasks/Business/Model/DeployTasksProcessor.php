<?php

namespace TurbineKreuzberg\Zed\DeployTasks\Business\Model;

use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use TurbineKreuzberg\Shared\DeployTasks\DeployTasksConstants;

class DeployTasksProcessor
{
    use TransactionTrait;

    /**
     * @var \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksFileReader
     */
    private DeployTasksFileReader $tasksFileReader;

    /**
     * @var \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksValidator
     */
    private DeployTasksValidator $tasksValidator;

    /**
     * @var \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksExecutor
     */
    private DeployTasksExecutor $tasksExecutor;

    /**
     * @var \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksVersionJournal
     */
    private DeployTasksVersionJournal $tasksVersionJournal;

    /**
     * @var \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksLogger
     */
    private DeployTasksLogger $tasksLogger;

    /**
     * @param \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksFileReader $tasksFileReader
     * @param \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksValidator $tasksValidator
     * @param \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksExecutor $tasksExecutor
     * @param \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksVersionJournal $tasksVersionJournal
     * @param \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksLogger $tasksLogger
     */
    public function __construct(
        DeployTasksFileReader $tasksFileReader,
        DeployTasksValidator $tasksValidator,
        DeployTasksExecutor $tasksExecutor,
        DeployTasksVersionJournal $tasksVersionJournal,
        DeployTasksLogger $tasksLogger
    ) {
        $this->tasksFileReader = $tasksFileReader;
        $this->tasksValidator = $tasksValidator;
        $this->tasksExecutor = $tasksExecutor;
        $this->tasksVersionJournal = $tasksVersionJournal;
        $this->tasksLogger = $tasksLogger;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->tasksLogger->writeOutput('Start processing deploy tasks');

        $taskFiles = $this->tasksFileReader->getFilesToExecute();

        if (count($taskFiles) === 0) {
            $this->tasksLogger->writeOutput('No deploy tasks left to execute');
        }

        foreach ($taskFiles as $timestamp => $taskFile) {
            $this->tasksLogger->printSeparatorLine();
            $this->tasksLogger->writeOutput(sprintf('Executing tasks group %s', $timestamp));
            $this->tasksLogger->printSeparatorLine();

            $tasks = $this->tasksFileReader->readTasksFromFile($taskFile);
            $validatedTasks = $this->tasksValidator->validate($tasks);

            if ($this->executeTasks($validatedTasks[DeployTasksConstants::YAML_KEY_TASKS])) {
                $this->tasksVersionJournal->saveExecutedVersion((int)$timestamp);
            }
        }

        $this->tasksLogger->writeOutput('Processing finished');
    }

    /**
     * @param array $tasks
     *
     * @return bool
     */
    private function executeTasks(array $tasks): bool
    {
        foreach ($tasks as $task) {
            if (!$this->tasksExecutor->execute($task)) {
                return false;
            }

            $this->tasksLogger->printSeparatorLine();
        }

        return true;
    }
}
