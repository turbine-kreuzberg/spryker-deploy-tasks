<?php

namespace TurbineKreuzberg\Shared\DeployTasks;

interface DeployTasksConstants
{
    /**
     * @var string
     */
    public const TASKS_FOLDER = 'DEPLOY_TASKS:TASKS_FOLDER';

    /**
     * @var string
     */
    public const PROCESS_TIMEOUT = 'DEPLOY_TASKS:PROCESS_TIMEOUT';

    /**
     * @var string
     */
    public const YAML_KEY_EXECUTE_FOR_STORE = 'execute_for_store';

    /**
     * @var string
     */
    public const YAML_KEY_EXECUTE_ON = 'execute_on';

    /**
     * @var string
     */
    public const YAML_KEY_COMMAND = 'command';

    /**
     * @var string
     */
    public const YAML_KEY_TASKS = 'tasks';

    /**
     * @var string
     */
    public const YAML_KEY_DESCRIPTION = 'description';
}
