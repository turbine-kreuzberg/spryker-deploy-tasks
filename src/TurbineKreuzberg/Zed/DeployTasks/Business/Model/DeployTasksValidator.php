<?php

namespace TurbineKreuzberg\Zed\DeployTasks\Business\Model;

use TurbineKreuzberg\Shared\DeployTasks\DeployTasksConstants;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\InvalidTaskFormatException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\NoTasksArrayException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\NoTasksKeyException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\TaskCommandNotAStringException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\TaskExecuteOnNotAnArrayException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\TaskMandatoryKeyMissingException;

class DeployTasksValidator
{
    /**
     * @param array<string, array<int, array<string, string|array<string>>>> $tasks
     *
     * @return array<string, array<int, array<string, string|array<string>>>>
     */
    public function validate(array $tasks): array
    {
        $this->validateStructure($tasks);
        $this->validateTasks($tasks[DeployTasksConstants::YAML_KEY_TASKS]);

        return $tasks;
    }

    /**
     * @param array<string, array<int, array<string, string|array<string>>>> $tasks
     *
     * @throws \TurbineKreuzberg\Zed\DeployTasks\Business\Exception\NoTasksKeyException
     * @throws \TurbineKreuzberg\Zed\DeployTasks\Business\Exception\NoTasksArrayException
     *
     * @return void
     */
    private function validateStructure(array $tasks): void
    {
        if (!isset($tasks[DeployTasksConstants::YAML_KEY_TASKS])) {
            throw new NoTasksKeyException();
        }

        if (!is_array($tasks[DeployTasksConstants::YAML_KEY_TASKS])) {
            throw new NoTasksArrayException();
        }
    }

    /**
     * @param array<int, array<string, string|array<string>>> $tasks
     *
     * @throws \TurbineKreuzberg\Zed\DeployTasks\Business\Exception\TaskCommandNotAStringException
     * @throws \TurbineKreuzberg\Zed\DeployTasks\Business\Exception\TaskExecuteOnNotAnArrayException
     * @throws \TurbineKreuzberg\Zed\DeployTasks\Business\Exception\InvalidTaskFormatException
     *
     * @return void
     */
    private function validateTasks(array $tasks): void
    {
        foreach ($tasks as $index => $task) {
            $taskNumber = $index + 1;

            if (!is_array($task)) {
                throw new InvalidTaskFormatException(sprintf('Task %d is not an associative array', $taskNumber));
            }

            $this->checkForMandatoryKeys($task, $taskNumber);

            if (!is_string($task[DeployTasksConstants::YAML_KEY_COMMAND])) {
                throw new TaskCommandNotAStringException(sprintf('Command for task %d is not a string', $taskNumber));
            }

            if (!is_array($task[DeployTasksConstants::YAML_KEY_EXECUTE_FOR_STORE])) {
                throw new TaskExecuteOnNotAnArrayException(
                    sprintf(
                        "Value for '%s' for task %d is not an array",
                        DeployTasksConstants::YAML_KEY_EXECUTE_FOR_STORE,
                        $taskNumber,
                    ),
                );
            }

            if (!is_array($task[DeployTasksConstants::YAML_KEY_EXECUTE_ON])) {
                throw new TaskExecuteOnNotAnArrayException(
                    sprintf(
                        "Value for '%s' for task %d is not an array",
                        DeployTasksConstants::YAML_KEY_EXECUTE_ON,
                        $taskNumber,
                    ),
                );
            }
        }
    }

    /**
     * @param array<string, string|array<string>> $task
     * @param int $taskNumber
     *
     * @throws \TurbineKreuzberg\Zed\DeployTasks\Business\Exception\TaskMandatoryKeyMissingException
     *
     * @return void
     */
    private function checkForMandatoryKeys(array $task, int $taskNumber): void
    {
        $mandatoryKeys = [
            DeployTasksConstants::YAML_KEY_COMMAND,
            DeployTasksConstants::YAML_KEY_EXECUTE_FOR_STORE,
            DeployTasksConstants::YAML_KEY_EXECUTE_ON,
        ];

        foreach ($mandatoryKeys as $mandatoryKey) {
            if (!isset($task[$mandatoryKey])) {
                throw new TaskMandatoryKeyMissingException(
                    sprintf(
                        "Task %d has no '%s' key",
                        $taskNumber,
                        $mandatoryKey,
                    ),
                );
            }
        }
    }
}
