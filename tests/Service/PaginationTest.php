<?php

namespace Test\Service;

use Pagination\Service\Adapter\AdapterInterface;
use Pagination\Service\Pagination;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    /** @var AdapterInterface&MockObject */
    private $adapter;

    public function setUp(): void
    {
        parent::setUp();

        $this->adapter = $this->createMock(AdapterInterface::class);
    }

    public function testInit(): void
    {
        try {
            new Pagination($this->adapter);
        } catch (\Throwable $throwable) {
            $this->fail();
        }

        self::assertTrue(true);
    }

    public function testSetterAndGetter(): void
    {
        $pagination = new Pagination($this->adapter);
        $pagination
            ->setCurrentPage(1)
            ->setPerPage(2);

        self::assertEquals(1, $pagination->getCurrentPage());
        self::assertEquals(2, $pagination->getPerPage());
    }

    public function dataProviderCalculate(): array
    {
        return [
            [1, 10, 0],
            [2, 10, 10],
            [5, 9, 36],
        ];
    }

    /**
     * @dataProvider dataProviderCalculate
     */
    public function testCalculate(int $currentPage, int $perPage, $expectedOffset): void
    {
        $pagination = new Pagination($this->adapter);
        $pagination
            ->setCurrentPage($currentPage)
            ->setPerPage($perPage)
            ->calculate();

        self::assertEquals($expectedOffset, $pagination->getOffset());
    }

    public function testExecute(): void
    {
        $this->adapter
            ->expects(self::once())
            ->method('apply')
            ->with(0, 2);

        $pagination = new Pagination($this->adapter);
        $pagination
            ->setCurrentPage(1)
            ->setPerPage(2)
            ->calculate();

        $pagination->execute();
    }

    public function dataProviderGetFirstPage(): array
    {
        return [
            [10, 10, false, 1],
            [10, 10, true, 5],
            [2, 10, true, 1],
        ];
    }

    /**
     * @dataProvider dataProviderGetFirstPage
     */
    public function testGetFirstPage(int $currentPage, int $perPage, bool $hideConfig, int $expectedPage): void
    {
        $pagination = new Pagination($this->adapter);
        $pagination
            ->setCurrentPage($currentPage)
            ->setPerPage($perPage)
            ->setConfig(Pagination::CONFIG_HIDE_FIRST_PAGES, $hideConfig);

        self::assertSame($expectedPage, $pagination->getFirstPage());
    }

    public function dataProviderGetLastPage(): array
    {
        return [
            [10, 5, false, 20],
            [25, 5, true, 20],
            [1, 5, true, 6],
        ];
    }

    /**
     * @dataProvider dataProviderGetLastPage
     */
    public function testGetLastPage(int $currentPage, int $perPage, bool $hideConfigLastPage, int $expectedPage): void
    {
        $this->adapter
            ->expects(self::once())
            ->method('getAmount')
            ->willReturn(100);

        $pagination = new Pagination($this->adapter);
        $pagination
            ->setCurrentPage($currentPage)
            ->setPerPage($perPage)
            ->setConfig(Pagination::CONFIG_HIDE_LAST_PAGES, $hideConfigLastPage);

        self::assertSame($expectedPage, $pagination->getLastPage());
    }

    public function dataProviderGetPageList(): array
    {
        return [
            [false, false, [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]],
            [false, true, [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]],
            [true, false, [5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]],
            [true, true, [5,6,7,8,9,10,11,12,13,14,15]],
        ];
    }

    /**
     * @dataProvider dataProviderGetPageList
     */
    public function testGetPageList(
        bool $hideConfigFirstPage,
        bool $hideConfigLastPage,
        array $expectedPages
    ): void {
        $this->adapter
            ->expects(self::once())
            ->method('getAmount')
            ->willReturn(100);

        $pagination = new Pagination($this->adapter);
        $pagination
            ->setCurrentPage(10)
            ->setPerPage(5)
            ->setConfig(Pagination::CONFIG_HIDE_FIRST_PAGES, $hideConfigFirstPage)
            ->setConfig(Pagination::CONFIG_HIDE_LAST_PAGES, $hideConfigLastPage);

        self::assertEquals(
            $expectedPages,
            $pagination->getPageList()
        );
    }
}