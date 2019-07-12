<?php

namespace Pagination\Service\Adapter;

class ArrayAdapter implements AdapterInterface
{
    /** @var array */
    private $data;
    /** @var array */
    private $appliedData;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getInputData(): array
    {
        return $this->data;
    }

    public function getAppliedData(): array
    {
        return $this->appliedData;
    }

    public function apply(int $offset, int $limit): void
    {
        $this->appliedData = array_slice($this->data, $offset, $limit);
    }

    public function getAmount(): int
    {
        return count($this->data);
    }
}
