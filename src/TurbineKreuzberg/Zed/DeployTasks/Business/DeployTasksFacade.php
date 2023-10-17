<?php

namespace TurbineKreuzberg\Zed\DeployTasks\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \TurbineKreuzberg\Zed\DeployTasks\Business\DeployTasksBusinessFactory getFactory()
 */
class DeployTasksFacade extends AbstractFacade implements DeployTasksFacadeInterface
{
    /**
     * @inheritDoc
     */
    public function executeTasks(): void
    {
        $this->getFactory()->createDeployTasksProcessor()->execute();
    }

    /**
     * @inheritDoc
     */
    public function generateNewTasksFile(): string
    {
        return $this->getFactory()->createDeployTasksGenerator()->createFile();
    }
}
