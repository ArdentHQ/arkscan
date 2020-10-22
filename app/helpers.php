<?php

declare(strict_types=1);

function truncateMiddle(string $value, int $length = 7): string
{
    $maxLength = $length + 3;

    if (strlen($value) <= $maxLength) {
        return $value;
    }

    return substr_replace($value, '...', ceil($maxLength / 2), strlen($value) - $maxLength);
}
