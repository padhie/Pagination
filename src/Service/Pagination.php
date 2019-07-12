<?php
namespace Pagination\Service;

use Pagination\Service\Adapter\AdapterInterface;

class Pagination
{
    public const CONFIG_HIDE_FIRST_PAGES = 'hide_first_page';
    public const CONFIG_HIDE_LAST_PAGES = 'hide_last_page';
    public const CONFIG_NUMBER_FIRST_PAGE_TO_HIDE = '1';
    public const CONFIG_NUMBER_LAST_PAGE_TO_HIDE = '2';

    /** @var array<string,bool|int> */
    private $config = [
        self::CONFIG_HIDE_FIRST_PAGES => true,
        self::CONFIG_HIDE_LAST_PAGES => true,
        self::CONFIG_NUMBER_FIRST_PAGE_TO_HIDE => 5,
        self::CONFIG_NUMBER_LAST_PAGE_TO_HIDE => 5,
    ];
    /** @var int */
    private $currentPage = 1;
    /** @var int */
    private $perPage = 0;
    /** @var int */
    private $offset = 0;
    /** @var AdapterInterface */
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function setConfig(string $config, $value): self
    {
        $this->config[$config] = $value;

        return $this;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getFirstPage(): int
    {
        if (!$this->config[self::CONFIG_HIDE_FIRST_PAGES]) {
            return 1;
        }

        if ($this->currentPage <= $this->config[self::CONFIG_NUMBER_FIRST_PAGE_TO_HIDE]) {
            return 1;
        }

        return $this->currentPage - $this->config[self::CONFIG_NUMBER_FIRST_PAGE_TO_HIDE];
    }

    public function getLastPage(): int
    {
        $amount = $this->adapter->getAmount();
        $lastPage = ceil($amount / $this->perPage);

        if (!$this->config[self::CONFIG_HIDE_LAST_PAGES]) {
            return $lastPage;
        }

        if ($this->currentPage > $lastPage) {
            return $lastPage;
        }

        return $this->currentPage + $this->config[self::CONFIG_NUMBER_LAST_PAGE_TO_HIDE];
    }

    /**
     * @return array<int,int>
     */
    public function getPageList(): array
    {
        $firstPage = $this->getFirstPage();
        $lastPage = $this->getLastPage();

        $pageList = [];
        for ($i=$firstPage; $i<=$lastPage; $i++) {
            $pageList[] = $i;
        }

        return $pageList;
    }

    public function calculate(): void
    {
        $this->offset = (($this->currentPage - 1) * $this->perPage);
    }

    public function execute(): void
    {
        $this->adapter->apply($this->offset, $this->perPage);
    }
}
