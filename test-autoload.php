<?php

declare(strict_types = 1);

$autoloader = static function ($className) {
    $className = ltrim($className, '\\');
    $classNameParts = explode('\\', $className);

    $filePathPartsSupport = [];
    if ($classNameParts[0] === 'TurbineKreuzbergTest') {
        array_shift($classNameParts);
        $application = array_shift($classNameParts);
        $bundle = array_shift($classNameParts);
        $className = implode(DIRECTORY_SEPARATOR, $classNameParts);
        $filePathPartsSupport = [
            __DIR__,
            'tests',
            'TurbineKreuzbergTest',
            $application,
            $bundle,
            '_support',
            $className . '.php',
        ];
    }

    if ($classNameParts[0] === 'PhpStan') {
        array_shift($classNameParts);
        $className = implode(DIRECTORY_SEPARATOR, $classNameParts);
        $filePathPartsSupport = [
            __DIR__,
            'tests',
            'PhpStan',
            $className . '.php',
        ];
    }

    if ($filePathPartsSupport) {
        $filePath = implode(DIRECTORY_SEPARATOR, $filePathPartsSupport);
        if (file_exists($filePath)) {
            require $filePath;

            return true;
        }
    }

    return false;
};

/** @phpstan-ignore-next-line */
spl_autoload_register($autoloader);
