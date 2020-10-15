<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [__DIR__.'/app', __DIR__.'/tests']);
    $parameters->set(Option::SETS, [SetList::CODE_QUALITY]);

    $services = $containerConfigurator->services();
    $services->set(TypedPropertyRector::class);
};
