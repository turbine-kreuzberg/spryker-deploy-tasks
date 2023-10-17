<?php

namespace TurbineKreuzberg\Zed\DeployTasks\Business\Model;

class DeployTasksLogger
{
    /**
     * @var string
     */
    private static string $separator;

    public function __construct()
    {
        self::$separator = str_repeat('~', 70);
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @param string $message
     * @param bool $addDateTime
     *
     * @return void
     */
    public function writeOutput(string $message, bool $addDateTime = true): void
    {
        $output = $addDateTime ? sprintf('[%s] ', date('Y-m-d H:i:s')) : '';
        $output .= $message . PHP_EOL;
        echo $output;
    }

    /**
     * @return void
     */
    public function printSeparatorLine(): void
    {
        $this->writeOutput(self::$separator, false);
    }
}
