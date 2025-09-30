<?php

declare(strict_types=1);

namespace VM\Infrastructure\Core\Constant;

class CoreConstant
{
    public const string ROUTES_GLOB_PATTERN = PROJECT_ROOT.'/src/Application/*/Route/*.php';

    public const string API_PREFIX = '/api';
}
