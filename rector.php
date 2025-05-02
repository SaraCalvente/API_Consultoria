<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return RectorConfig::configure()
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class,
        ClassPropertyAssignToConstructorPromotionRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true
    );

