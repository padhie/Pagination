<?php

namespace Pagination\Service\Adapter;

interface AdapterInterface
{
    public function getAmount(): int;
    public function apply(int $offset, int $limit): void;
}
