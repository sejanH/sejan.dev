<?php

namespace App\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;

class HomePagePaginator extends LengthAwarePaginator
{
    public const FIRST_PAGE_COUNT = 13;
    public const SUBSEQUENT_PAGE_COUNT = 12;

    /**
     * Get the total number of pages.
     */
    public function lastPage(): int
    {
        if ($this->total <= self::FIRST_PAGE_COUNT) {
            return 1;
        }

        return (int) (1 + ceil(($this->total - self::FIRST_PAGE_COUNT) / self::SUBSEQUENT_PAGE_COUNT));
    }

    /**
     * Determine if there are more items in the data source.
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage() < $this->lastPage();
    }

    /**
     * Get the number of the first item in the slice.
     */
    public function firstItem(): ?int
    {
        if ($this->count() === 0) {
            return null;
        }

        if ($this->currentPage() === 1) {
            return 1;
        }

        return self::FIRST_PAGE_COUNT + 1 + ($this->currentPage() - 2) * self::SUBSEQUENT_PAGE_COUNT;
    }

    /**
     * Get the number of the last item in the slice.
     */
    public function lastItem(): ?int
    {
        if ($this->count() === 0) {
            return null;
        }

        return $this->firstItem() + $this->count() - 1;
    }
}
