<?php

declare(strict_types=1);

use Tests\DuskTestCase;
use Tests\TestCase;

uses(DuskTestCase::class)->in('Browser');

uses(TestCase::class)->in('Analysis', 'Feature', 'Unit');
