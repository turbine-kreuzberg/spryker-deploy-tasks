<?php

namespace TurbineKreuzbergTest\Zed\DeployTasks\Business;

use Codeception\Test\Unit;
use TurbineKreuzberg\Shared\DeployTasks\DeployTasksConstants;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksExecutor;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksLogger;
use TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig;
use TurbineKreuzbergTest\Zed\DeployTasks\DeployTasksBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group TurbineKreuzbergTest
 * @group Zed
 * @group DeployTasks
 * @group Business
 * @group DeployTasksExecutorTest
 * Add your own group annotations below this line
 */
class DeployTasksExecutorTest extends Unit
{
    /**
     * @var \TurbineKreuzbergTest\Zed\DeployTasks\DeployTasksBusinessTester $tester
     */
    protected DeployTasksBusinessTester $tester;

    /**
     * @dataProvider tasksToExecuteDataProvider
     *
     * @param array $task
     * @param bool $expectedResult
     * @param string|null $expectedOutput
     *
     * @return void
     */
    public function testExecuteTaskShouldReturnCorrectResult(
        array $task,
        bool $expectedResult,
        ?string $expectedOutput = null
    ): void {
        $result = $this->createDeployTasksExecutor()->execute($task);

        codecept_debug($task);
        codecept_debug($this->getActualOutput());

        self::assertEquals($expectedResult, $result);

        if ($expectedOutput !== null) {
            self::assertStringContainsString($expectedOutput, $this->getActualOutput());
        }
    }

    /**
     * @return \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksExecutor
     */
    private function createDeployTasksExecutor(): DeployTasksExecutor
    {
        return new DeployTasksExecutor(
            new DeployTasksConfig(),
            new DeployTasksLogger(),
        );
    }

    /**
     * @return array
     */
    public function tasksToExecuteDataProvider(): array
    {
        $currentStore = getenv('APPLICATION_STORE');
        $otherStores = ['someOtherStore1', 'someOtherStore2'];
        $storesIncludingCurrentStore = array_merge([$currentStore], $otherStores);
        $currentEnvironment = getenv('SHOP_ENV');
        $otherEnvironments = ['someOtherEnvironment1', 'someOtherEnvironment2'];
        $environmentsIncludingCurrentEnvironment = array_merge([$currentEnvironment], $otherEnvironments);
        $validCommand = 'date';

        return [
            'command-executable-in-current-env' => [
                'task' => [
                    DeployTasksConstants::YAML_KEY_COMMAND => $validCommand,
                    DeployTasksConstants::YAML_KEY_EXECUTE_FOR_STORE => $storesIncludingCurrentStore,
                    DeployTasksConstants::YAML_KEY_EXECUTE_ON => $environmentsIncludingCurrentEnvironment,
                ],
                'expectedResult' => true,
                'expectedOutput' => '',
            ],
            'command-skipped-in-current-store' => [
                'task' => [
                    DeployTasksConstants::YAML_KEY_COMMAND => $validCommand,
                    DeployTasksConstants::YAML_KEY_EXECUTE_FOR_STORE => $otherStores,
                    DeployTasksConstants::YAML_KEY_EXECUTE_ON => $environmentsIncludingCurrentEnvironment,
                ],
                'expectedResult' => true,
                'expectedOutput' => sprintf(
                    "Command '%s' will be skipped for store '%s'",
                    $validCommand,
                    $currentStore,
                ),
            ],
            'command-skipped-in-current-env' => [
                'task' => [
                    DeployTasksConstants::YAML_KEY_COMMAND => $validCommand,
                    DeployTasksConstants::YAML_KEY_EXECUTE_FOR_STORE => $storesIncludingCurrentStore,
                    DeployTasksConstants::YAML_KEY_EXECUTE_ON => $otherEnvironments,
                ],
                'expectedResult' => true,
                'expectedOutput' => sprintf(
                    "Command '%s' will be skipped in env '%s'",
                    $validCommand,
                    $currentEnvironment,
                ),
            ],
            'invalid-command-returns-false' => [
                'task' => [
                    DeployTasksConstants::YAML_KEY_COMMAND => '/bin/some-invalid-command',
                    DeployTasksConstants::YAML_KEY_EXECUTE_FOR_STORE => $storesIncludingCurrentStore,
                    DeployTasksConstants::YAML_KEY_EXECUTE_ON => $environmentsIncludingCurrentEnvironment,
                ],
                'expectedResult' => false,
                'expectedOutput' => 'Task failed',
            ],
            'task-with-description' => [
                'task' => [
                    DeployTasksConstants::YAML_KEY_COMMAND => $validCommand,
                    DeployTasksConstants::YAML_KEY_EXECUTE_FOR_STORE => $storesIncludingCurrentStore,
                    DeployTasksConstants::YAML_KEY_EXECUTE_ON => $environmentsIncludingCurrentEnvironment,
                    DeployTasksConstants::YAML_KEY_DESCRIPTION => 'foo',
                ],
                'expectedResult' => true,
                'expectedOutput' => 'Description: foo',
            ],
        ];
    }
}
