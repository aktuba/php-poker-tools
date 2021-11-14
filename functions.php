<?php

declare(strict_types=1);

if (!function_exists('dd')) {
    function dd(...$arguments)
    {
        dump(...$arguments);
        die(0);
    }
}
