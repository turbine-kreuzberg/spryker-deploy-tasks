<?php

namespace TurbineKreuzberg\Zed\DeployTasks\Business;

use Orm\Zed\DeployTasks\Persistence\TxbDeployTasksQuery;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksExecutor;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksFileReader;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksGenerator;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksLogger;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksProcessor;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksValidator;
use TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksVersionJournal;

/**
 * @method \TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig getConfig()
 */
class DeployTasksBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksProcessor
     */
    public function createDeployTasksProcessor(): DeployTasksProcessor
    {
        return new DeployTasksProcessor(
            $this->createDeployTasksFileReader(),
            $this->createDeployTasksValidator(),
            $this->createDeployTasksExecutor(),
            $this->createDeployTasksJournal(),
            $this->createDeployTasksLogger(),
        );
    }

    /**
     * @return \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksGenerator
     */
    public function createDeployTasksGenerator(): DeployTasksGenerator
    {
        return new DeployTasksGenerator($this->getConfig());
    }

    /**
     * @return \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksFileReader
     */
    private function createDeployTasksFileReader(): DeployTasksFileReader
    {
        return new DeployTasksFileReader(
            $this->getConfig(),
            $this->createDeployTasksJournal(),
        );
    }

    /**
     * @return \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksValidator
     */
    private function createDeployTasksValidator(): DeployTasksValidator
    {
        return new DeployTasksValidator();
    }

    /**
     * @return \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksExecutor
     */
    private function createDeployTasksExecutor(): DeployTasksExecutor
    {
        return new DeployTasksExecutor(
            $this->getConfig(),
            $this->createDeployTasksLogger(),
        );
    }

    /**
     * @return \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksVersionJournal
     */
    private function createDeployTasksJournal(): DeployTasksVersionJournal
    {
        return new DeployTasksVersionJournal(
            $this->getDeployTasksQuery(),
        );
    }

    /**
     * @return \Orm\Zed\DeployTasks\Persistence\TxbDeployTasksQuery
     */
    private function getDeployTasksQuery(): TxbDeployTasksQuery
    {
        return TxbDeployTasksQuery::create();
    }

    /**
     * @return \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksLogger
     */
    private function createDeployTasksLogger(): DeployTasksLogger
    {
        return new DeployTasksLogger();
    }
}
