<?php

namespace TurbineKreuzbergTest\Zed\DeployTasks\Business;

use Codeception\Test\Unit;
use Orm\Zed\DeployTasks\Persistence\TxbDeployTasksQuery;
use Propel\Runtime\Exception\PropelException;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksVersionJournal;
use TurbineKreuzbergTest\Zed\DeployTasks\DeployTasksBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group TurbineKreuzbergTest
 * @group Zed
 * @group DeployTasks
 * @group Business
 * @group DeployTasksVersionJournalTest
 * Add your own group annotations below this line
 */
class DeployTasksVersionJournalTest extends Unit
{
    /**
     * @var \TurbineKreuzbergTest\Zed\DeployTasks\DeployTasksBusinessTester $tester
     */
    protected DeployTasksBusinessTester $tester;

    /**
     * @return void
     */
    public function testSaveVersionToVersionJournalShouldWriteEntryInDb(): void
    {
        codecept_debug(APPLICATION_ROOT_DIR);

        $versionJournal = $this->createDeployTasksVersionJournal();
        $version = time();

        $countAllVersionsBefore = $this->createDeployTasksQuery()->find()->count();
        $existVersionBefore = $this->createDeployTasksQuery()->filterByVersion($version)->exists();

        self::assertFalse($existVersionBefore);

        $versionJournal->saveExecutedVersion($version);

        $countAllVersionsAfter = $this->createDeployTasksQuery()->find()->count();
        $existVersionAfter = $this->createDeployTasksQuery()->filterByVersion($version)->exists();

        self::assertEquals($countAllVersionsBefore + 1, $countAllVersionsAfter);
        self::assertTrue($existVersionAfter);
    }

    /**
     * @return void
     */
    public function testSaveSameVersionTwiceToVersionJournalShouldFail(): void
    {
        $versionJournal = $this->createDeployTasksVersionJournal();

        $version = time();
        $versionJournal->saveExecutedVersion($version);

        $this->expectException(PropelException::class);
        $this->expectExceptionMessageMatches('/(Unique violation|Unable to execute INSERT statement)/');

        $versionJournal->saveExecutedVersion($version);
    }

    /**
     * @return void
     */
    public function testGetExecutedVersionsFromVersionJournalShouldReturnAllEntriesFromDb(): void
    {
        $versionJournal = $this->createDeployTasksVersionJournal();
        $executedVersionsBefore = $versionJournal->getExecutedVersions();
        $countAllBefore = $this->createDeployTasksQuery()->count();

        self::assertCount($countAllBefore, $executedVersionsBefore);

        $version = time();
        mt_srand();
        $max = random_int(0, 10);
        $versions = [];

        for ($i = 0; $i < $max; $i++) {
            $versionJournal->saveExecutedVersion($version + $i);
            $versions[] = $version + $i;
        }

        $executedVersionsAfter = $versionJournal->getExecutedVersions();
        self::assertCount($countAllBefore + $max, $executedVersionsAfter);

        foreach ($versions as $version) {
            self::assertArrayHasKey($version, $executedVersionsAfter);
        }
    }

    /**
     * @return \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksVersionJournal
     */
    private function createDeployTasksVersionJournal(): DeployTasksVersionJournal
    {
        return new DeployTasksVersionJournal(
            $this->createDeployTasksQuery(),
        );
    }

    /**
     * @return \Orm\Zed\DeployTasks\Persistence\TxbDeployTasksQuery
     */
    private function createDeployTasksQuery(): TxbDeployTasksQuery
    {
        return TxbDeployTasksQuery::create();
    }
}
