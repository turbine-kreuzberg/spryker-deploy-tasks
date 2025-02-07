<?php

namespace TurbineKreuzbergTest\Zed\DeployTasks\Business;

use Codeception\Test\Unit;
use Symfony\Component\Yaml\Yaml;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\InvalidTaskFormatException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\NoTasksArrayException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\NoTasksKeyException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\TaskCommandNotAStringException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\TaskExecuteOnNotAnArrayException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Exception\TaskMandatoryKeyMissingException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksValidator;
use TurbineKreuzbergTest\Zed\DeployTasks\DeployTasksBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group TurbineKreuzbergTest
 * @group Zed
 * @group DeployTasks
 * @group Business
 * @group DeployTasksValidatorTest
 * Add your own group annotations below this line
 */
class DeployTasksValidatorTest extends Unit
{
    /**
     * @var \TurbineKreuzbergTest\Zed\DeployTasks\DeployTasksBusinessTester $tester
     */
    protected DeployTasksBusinessTester $tester;

    /**
     * @dataProvider invalidTasksDataProvider
     *
     * @param string $file
     * @param string|null $expectedException
     *
     * @return void
     */
    public function testTasksFilesAreCorrectlyValidated(string $file, ?string $expectedException = null): void
    {
        $ymlFile = codecept_data_dir('validator') . DIRECTORY_SEPARATOR . $file;
        $tasks = (array)Yaml::parse((string)file_get_contents($ymlFile));
        codecept_debug($tasks);

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $validator = new DeployTasksValidator();
        $validator->validate($tasks);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function invalidTasksDataProvider(): array
    {
        return [
            'valid-tasks' => [
                'file' => 'tasks.valid.yml',
            ],
            'missing-tasks-key' => [
                'file' => 'tasks.invalid.missing-tasks.yml',
                'expectedException' => NoTasksKeyException::class,
            ],
            'tasks-not-an-array' => [
                'file' => 'tasks.invalid.tasks-not-an-array.yml',
                'expectedException' => NoTasksArrayException::class,
            ],
            'task-not-an-array' => [
                'file' => 'tasks.invalid.task-not-an-array.yml',
                'expectedException' => InvalidTaskFormatException::class,
            ],
            'task-key-command-missing' => [
                'file' => 'tasks.invalid.key-command-missing.yml',
                'expectedException' => TaskMandatoryKeyMissingException::class,
            ],
            'task-key-execute_on-missing' => [
                'file' => 'tasks.invalid.key-execute_on-missing.yml',
                'expectedException' => TaskMandatoryKeyMissingException::class,
            ],
            'task-key-execute_for_store-missing' => [
                'file' => 'tasks.invalid.key-execute_for_store-missing.yml',
                'expectedException' => TaskMandatoryKeyMissingException::class,
            ],
            'task-command-is-not-a-string' => [
                'file' => 'tasks.invalid.task-command-not-string.yml',
                'expectedException' => TaskCommandNotAStringException::class,
            ],
            'task-execute_on-not-an-array' => [
                'file' => 'tasks.invalid.execute_on-not-an-array.yml',
                'expectedException' => TaskExecuteOnNotAnArrayException::class,
            ],
        ];
    }
}
