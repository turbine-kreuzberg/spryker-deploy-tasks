<?php

namespace TurbineKreuzberg\Zed\DeployTasks\Business\Model;

use TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig;

class DeployTasksGenerator
{
    /**
     * @var \TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig
     */
    private DeployTasksConfig $config;

    /**
     * @param \TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig $config
     */
    public function __construct(DeployTasksConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function createFile(): string
    {
        $tasksFolder = $this->config->getTasksFolder();
        $tasksFileName = sprintf($this->config->getTasksFilePattern(), time());

        $fullPath = sprintf('%s/%s', rtrim($tasksFolder, '/'), $tasksFileName);

        file_put_contents($fullPath, $this->getTasksTemplate());

        return $fullPath;
    }

    /**
     * @return string
     */
    private function getTasksTemplate(): string
    {
        return <<<EOF
tasks:
# add new deploy tasks as an array to this file

# a task has to be an item with the mandatory keys 'command', 'execute_for_store' and 'execute_on'
# - the value for 'command' should be an executable shell command line (multiple commands and options possible)
# - the entries for 'execute_for_store' have to match the value of env var APPLICATION_STORE
# - the entries for 'execute_on' have to match the value of env var SHOP_ENV (dev,int,stage,prd)
  - command: "some/command/to/execute --with-option --another-option-with-value foo && some/other/command"
    execute_for_store:
      - DE
      - EN
    execute_on:
      - dev
      - int
      - stage
      - prd
# to skip executing a task in a particular environment, it needs to be
# removed from the 'execute_on' list
  - command: "some/other/command/to/execute/everywhere/but/production"
    execute_for_store:
      - DE
      - EN
    execute_on:
      - dev
      - int
      - stage
# to skip executing a task in a particular store, it needs to be
# removed from the 'execute_for_store' list
  - command: "some/other/command/to/execute/everywhere/only/for/store-en"
    execute_for_store:
      - EN
    execute_on:
      - dev
      - int
      - stage
      - prd
# task can have additional optional keys, e.g. 'description'
  - command: "yet/another/command/to/execute"
    description: "description for yet another command to execute"
    execute_for_store:
      - DE
      - EN
    execute_on:
      - dev
      - int
      - stage
      - prd
EOF;
    }
}
