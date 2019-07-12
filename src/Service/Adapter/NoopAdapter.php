<?php

namespace Pagination\Service\Adapter;

class NoopAdapter implements AdapterInterface
{
    public function apply(int $offset, int $limit): void
    {
    }

    public function getAmount(): int
    {
        return 0;
    }
}
