<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 10:06 PM
 */

namespace App\Timeline\Domain\Requests;


use App\Timeline\App\Validators\ValidatorFactory;
use App\Timeline\Utils\JsonSerializable;

class PageableRequest implements JsonSerializable
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
     * PageableRequest constructor.
     * @param int $page
     * @param int $pageSize
     */
    public function __construct(int $page = 1, int $pageSize = 10)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    public static function createFromValueArray(?array $data): ?self
    {
        if($data === null) {
            return null;
        }

        resolve(ValidatorFactory::class)->validate($data, [
            'page' => 'nullable|integer|gt:0',
            'pageSize' => 'nullable|integer|gt:0'
        ]);

        $page = $data['page'] ?? 1;
        $pageSize = $data['pageSize'] ?? 10;

        return new static($page, $pageSize);
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
    public function getOffset(): int
    {
        return ($this->page - 1) * $this->pageSize;
    }

    public function toValueArray(): array
    {
        return [
            'page' => $this->getPage(),
            'pageSize' => $this->getPageSize()
        ];
    }
}