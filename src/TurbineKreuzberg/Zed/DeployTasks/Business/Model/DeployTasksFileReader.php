<?php

namespace TurbineKreuzberg\Zed\DeployTasks\Business\Model;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig;

class DeployTasksFileReader
{
    /**
     * @var \TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig
     */
    private DeployTasksConfig $config;

    /**
     * @var \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksVersionJournal
     */
    private DeployTasksVersionJournal $tasksVersionJournal;

    /**
     * @param \TurbineKreuzberg\Zed\DeployTasks\DeployTasksConfig $config
     * @param \TurbineKreuzberg\Zed\DeployTasks\Business\Model\DeployTasksVersionJournal $tasksVersionJournal
     */
    public function __construct(
        DeployTasksConfig $config,
        DeployTasksVersionJournal $tasksVersionJournal
    ) {
        $this->config = $config;
        $this->tasksVersionJournal = $tasksVersionJournal;
    }

    /**
     * @return array<string, string>
     */
    public function getFilesToExecute(): array
    {
        $taskFiles = $this->getTaskFiles();
        $executedVersions = $this->tasksVersionJournal->getExecutedVersions();

        return array_diff_key($taskFiles, $executedVersions);
    }

    /**
     * @param string $filePath
     *
     * @return array<string, array<int, array<string, string|array<string>>>>
     */
    public function readTasksFromFile(string $filePath): array
    {
        return (array)Yaml::parse((string)file_get_contents($filePath));
    }

    /**
     * @return array<string, string>
     */
    private function getTaskFiles(): array
    {
        $tasksFolder = $this->config->getTasksFolder();
        $tasksFilePattern = sprintf($this->config->getTasksFilePattern(), '*');

        $finder = new Finder();
        $finder->in($tasksFolder)
            ->name($tasksFilePattern)
            ->files();

        if (!$finder->hasResults()) {
            return [];
        }

        return $this->prepareTasksFilesArray($finder);
    }

    /**
     * @param \Symfony\Component\Finder\Finder $finder
     *
     * @return array<string, string>
     */
    private function prepareTasksFilesArray(Finder $finder): array
    {
        $taskFiles = [];

        foreach ($finder as $file) {
            $timestamp = $this->extractTimestampFromFilename($file->getRelativePathname());

            if ($timestamp === null) {
                continue;
            }

            $realPath = $file->getRealPath();

            if ($realPath === false) {
                continue;
            }

            $taskFiles[$timestamp] = $realPath;
        }

        ksort($taskFiles, SORT_NUMERIC);

        return $taskFiles;
    }

    /**
     * @param string $fileName
     *
     * @return string|null
     */
    private function extractTimestampFromFilename(string $fileName): ?string
    {
        $pattern = sprintf($this->config->getTasksFilePattern(), '([0-9]+)');
        $matches = [];

        preg_match('/' . $pattern . '/', $fileName, $matches);

        return $matches[1] ?? null;
    }
}
