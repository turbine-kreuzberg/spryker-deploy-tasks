<?php

namespace TurbineKreuzbergTest\Zed\DeployTasks\Business;

use Codeception\Test\Unit;
use Orm\Zed\DeployTasks\Persistence\TxbDeployTasksQuery;
use TurbineKreuzberg\Shared\DeployTasks\DeployTasksConstants;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksFileReader;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksVersionJournal;
use TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig;
use TurbineKreuzbergTest\Zed\DeployTasks\DeployTasksBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group TurbineKreuzbergTest
 * @group Zed
 * @group DeployTasks
 * @group Business
 * @group DeployTasksFileReaderTest
 * Add your own group annotations below this line
 */
class DeployTasksFileReaderTest extends Unit
{
    /**
     * @var \TurbineKreuzbergTest\Zed\DeployTasks\DeployTasksBusinessTester $tester
     */
    protected DeployTasksBusinessTester $tester;

    /**
     * @var \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksFileReader
     */
    private DeployTasksFileReader $tasksFileReader;

    /**
     * @return void
     */
    private function config(): void
    {
        $this->tester->setConfig(DeployTasksConstants::TASKS_FOLDER, codecept_data_dir('file-reader'));
        $this->tasksFileReader = $this->createDeployTasksFileReader();
    }

    /**
     * @return void
     */
    public function testFileReaderReturnsCorrectArrayOfFilesToBeExecuted(): void
    {
        $this->config();

        $filesToExecute = $this->tasksFileReader->getFilesToExecute();
        codecept_debug(compact('filesToExecute'));

        $countAllFiles = 4;
        self::assertCount($countAllFiles, $filesToExecute);

        $firstVersionExecuted = 123456789;
        $this->tester->haveVersionJournalEntry($firstVersionExecuted);

        $filesToExecuteWithoutExecutedVersion = $this->tasksFileReader->getFilesToExecute();
        codecept_debug(compact('filesToExecuteWithoutExecutedVersion'));

        $countAllFiles--;
        self::assertCount($countAllFiles, $filesToExecuteWithoutExecutedVersion);
        self::assertArrayNotHasKey($firstVersionExecuted, $filesToExecuteWithoutExecutedVersion);

        $secondVersionExecuted = 123;
        $this->tester->haveVersionJournalEntry($secondVersionExecuted);

        $filesToExecuteWithoutTwoExecutedVersions = $this->tasksFileReader->getFilesToExecute();
        codecept_debug(compact('filesToExecuteWithoutTwoExecutedVersions'));

        $countAllFiles--;
        self::assertCount($countAllFiles, $filesToExecuteWithoutTwoExecutedVersions);
        self::assertArrayNotHasKey($firstVersionExecuted, $filesToExecuteWithoutTwoExecutedVersions);
        self::assertArrayNotHasKey($secondVersionExecuted, $filesToExecuteWithoutTwoExecutedVersions);
    }

    /**
     * @return void
     */
    public function testFileReaderReturnsArrayWithCorrectOrderOfVersionKeys(): void
    {
        $this->config();

        $filesToExecute = $this->tasksFileReader->getFilesToExecute();
        codecept_debug(compact('filesToExecute'));

        $versions = array_keys($filesToExecute);

        $previousVersion = null;

        foreach ($versions as $version) {
            self::assertGreaterThan(0, $version);

            // @phpstan-ignore-next-line
            if ($previousVersion !== null) {
                self::assertGreaterThan($previousVersion, $version);
            }

            $previousVersion = $version;
        }
    }

    /**
     * @return \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksFileReader
     */
    private function createDeployTasksFileReader(): DeployTasksFileReader
    {
        return new DeployTasksFileReader(
            new DeployTasksConfig(),
            new DeployTasksVersionJournal(
                TxbDeployTasksQuery::create(),
            ),
        );
    }
}
