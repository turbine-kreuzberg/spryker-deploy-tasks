<?php

namespace TurbineKreuzbergTest\Zed\DeployTasks\Business;

use Codeception\Test\Unit;
use TurbineKreuzberg\Shared\DeployTasks\DeployTasksConstants;
use TurbineKreuzberg\Zed\DeployTasks\Business\DeployTasksBusinessFactory;
use TurbineKreuzbergTest\Zed\DeployTasks\DeployTasksBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group TurbineKreuzbergTest
 * @group Zed
 * @group DeployTasks
 * @group Business
 * @group DeployTasksGeneratorTest
 * Add your own group annotations below this line
 */
class DeployTasksGeneratorTest extends Unit
{
    /**
     * @var \TurbineKreuzbergTest\Zed\DeployTasks\DeployTasksBusinessTester $tester
     */
    protected DeployTasksBusinessTester $tester;

    /**
     * @return void
     */
    public function testGeneratorCreatesCorrectFile(): void
    {
        $this->tester->setConfig(DeployTasksConstants::TASKS_FOLDER, codecept_output_dir());

        $factory = new DeployTasksBusinessFactory();
        $generator = $factory->createDeployTasksGenerator();

        $fileName = $generator->createFile();
        codecept_debug('Generated file: ' . $fileName);

        self::assertFileExists($fileName);

        unlink($fileName);
    }
}
