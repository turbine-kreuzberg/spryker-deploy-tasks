<?php

namespace TurbineKreuzberg\Zed\DeployTasks\Business\Model;

use Orm\Zed\DeployTasks\Persistence\Map\TxbDeployTasksTableMap;
use Orm\Zed\DeployTasks\Persistence\TxbDeployTasks;
use Orm\Zed\DeployTasks\Persistence\TxbDeployTasksQuery;

class DeployTasksVersionJournal
{
    /**
     * @var \Orm\Zed\DeployTasks\Persistence\TxbDeployTasksQuery
     */
    private TxbDeployTasksQuery $tasksQuery;

    /**
     * @param \Orm\Zed\DeployTasks\Persistence\TxbDeployTasksQuery $tasksQuery
     */
    public function __construct(TxbDeployTasksQuery $tasksQuery)
    {
        $this->tasksQuery = $tasksQuery;
    }

    /**
     * @return array<string>
     */
    public function getExecutedVersions(): array
    {
        /** @var \Propel\Runtime\Collection\ArrayCollection $collection */
        $collection = $this->tasksQuery
            ->select(TxbDeployTasksTableMap::COL_VERSION)
            ->find();

        $executedVersions = $collection->toArray();

        return array_flip($executedVersions);
    }

    /**
     * @param int $version
     *
     * @return void
     */
    public function saveExecutedVersion(int $version): void
    {
        $pyzDeployTasksEntity = new TxbDeployTasks();
        $pyzDeployTasksEntity
            ->setVersion($version)
            ->save();
    }
}
