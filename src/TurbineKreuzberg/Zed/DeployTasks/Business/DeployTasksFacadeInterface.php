<?php

namespace TurbineKreuzberg\Zed\DeployTasks\Business;

interface DeployTasksFacadeInterface
{
    /**
     * @return void
     */
    public function executeTasks(): void;

    /**
     * @return string
     */
    public function generateNewTasksFile(): string;
}
