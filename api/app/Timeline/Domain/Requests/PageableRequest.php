<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 10:06 PM
 */

namespace App\Timeline\Domain\Requests;


use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Utils\Common;

class PageableRequest
{
    /**
     * @var int
     */
    private $page;
    /**
     * @var int
     */
    private $pageSize;
    /**
     * @var int
     */
    private $maxPageSize;

    /**
     * PageableRequest constructor.
     * @param int $page
     * @param int $pageSize
     * @param int $maxPageSize
     * @throws TimelineException
     */
    public function __construct(int $page = 1, int $pageSize = 10, int $maxPageSize = 100)
    {
        $this->validatePage($page);
        $this->validatePageSize($pageSize, $maxPageSize);
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->maxPageSize = $maxPageSize;
    }

    /**
     * @param array $data
     * @param int $maxPageSize
     * @return PageableRequest
     * @throws TimelineException
     */
    public static function createFromArray(array $data, int $maxPageSize = 100): self {
        if(isset($data['page']) && !Common::isPosInt($data['page'])) {
            throw TimelineException::ofInvalidPageNumber($data['page']);
        }

        if(isset($data['pageSize']) && !Common::isPosInt($data['pageSize'])) {
            throw TimelineException::ofInvalidPageSize($data['pageSize']);
        }

        $page = $data['page'] ?? 1;
        $pageSize = $data['pageSize'] ?? 10;

        return new static($page, $pageSize, $maxPageSize);
    }

    /**
     * @param int $page
     * @throws TimelineException
     */
    private function validatePage(int $page): void
    {
        if ($page < 1) {
            throw TimelineException::ofInvalidPageNumber($page);
        }
    }

    /**
     * @param int $pageSize
     * @param int $maxPageSize
     * @throws TimelineException
     */
    private function validatePageSize(int $pageSize, int $maxPageSize): void
    {
        if ($pageSize < 1) {
            throw TimelineException::ofInvalidPageSize($pageSize);
        }

        if ($pageSize > $maxPageSize) {
            throw TimelineException::ofPageSizeTooLarge($pageSize);
        }
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @return int
     */
    public function getMaxPageSize(): int
    {
        return $this->maxPageSize;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return ($this->page - 1) * $this->pageSize;
    }
}