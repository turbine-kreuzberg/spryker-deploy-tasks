<?php

use Spryker\Shared\ErrorHandler\ErrorHandlerConstants;
use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Shared\Log\LogConstants;

$config[KernelConstants::PROJECT_NAMESPACE] = 'Pyz';
$config[KernelConstants::PROJECT_NAMESPACES] = [
    'Pyz',
];
$config[KernelConstants::CORE_NAMESPACES] = [
    'TurbineKreuzberg',
    'SprykerShop',
    'SprykerMiddleware',
    'SprykerEco',
    'Spryker',
];
$config[KernelConstants::PROJECT_NAMESPACE] = 'Pyz';
$config[ErrorHandlerConstants::ERROR_LEVEL] = 8191;
$config[KernelConstants::SPRYKER_ROOT] = APPLICATION_ROOT_DIR . '/../../vendor/spryker';
$config[LogConstants::LOG_LEVEL] = LOG_DEBUG;

require __DIR__ . '/../config_propel.php';
