<?php

namespace TurbineKreuzbergTest\Zed\DeployTasks\Helper;

use Codeception\Module;
use Orm\Zed\DeployTasks\Persistence\TxbDeployTasksQuery;
use SprykerTest\Client\Testify\Helper\ConfigHelperTrait;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;

class DeployTasksDataHelper extends Module
{
    use DataCleanupHelperTrait;
    use ConfigHelperTrait;

    /**
     * @return void
     */
    public function _initialize(): void
    {
        if (!defined('APPLICATION_STORE')) {
            define('APPLICATION_STORE', 'DE');
        }

        $this->getConfigHelper()->setConfig('PROJECT_NAMESPACES', ['TurbineKreuzberg']);
        $this->getConfigHelper()->setConfig(
            'CORE_NAMESPACES',
            [
            'Spryker',
            ],
        );
    }

    /**
     * @param int $version
     *
     * @return void
     */
    public function haveVersionJournalEntry(int $version): void
    {
        $pyzDeployTasks = TxbDeployTasksQuery::create()
            ->filterByVersion($version)
            ->findOneOrCreate();

        $pyzDeployTasks
            ->setVersion($version)
            ->save();

        $this->getDataCleanupHelper()->_addCleanup(
            static function () use ($version) {
                TxbDeployTasksQuery::create()
                    ->filterByVersion($version)
                    ->delete();
            },
        );
    }
}
