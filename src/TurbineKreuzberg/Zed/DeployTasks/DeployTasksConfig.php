<?php

namespace TurbineKreuzberg\Zed\DeployTasks;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use TurbineKreuzberg\Shared\DeployTasks\DeployTasksConstants;

class DeployTasksConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getTasksFolder(): string
    {
        return $this->get(
            DeployTasksConstants::TASKS_FOLDER,
            APPLICATION_ROOT_DIR . '/config/deploy-tasks/',
        );
    }

    /**
     * @return string
     */
    public function getTasksFilePattern(): string
    {
        return 'tasks.%s.yml';
    }

    /**
     * @return int
     */
    public function getProcessTimeout(): int
    {
        return $this->get(
            DeployTasksConstants::PROCESS_TIMEOUT,
            300,
        );
    }
}
