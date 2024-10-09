<?php

namespace App\Interface;

use Psr\Http\Message\StreamInterface;

interface ApiInterface
{
    public function fetch(string $method, string $endpoint, array $options = []): array | StreamInterface;
}