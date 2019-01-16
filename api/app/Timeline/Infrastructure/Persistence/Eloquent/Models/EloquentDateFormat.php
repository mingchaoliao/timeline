<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 5:09 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EloquentDateFormat extends Model
{
    protected $table = 'date_formats';
    protected $guarded = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function getMysqlFormat(): string
    {
        return $this->mysql_format;
    }

    public function getPhpFormat(): string
    {
        return $this->php_format;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->updated_at;
    }

    public function hasYear(): bool {
        return $this->has_year === 1 ? true : false;
    }

    public function hasMonth(): bool {
        return $this->has_month === 1 ? true : false;
    }

    public function hasDay(): bool {
        return $this->has_day === 1 ? true : false;
    }

    public function isAttributeAllowed(): bool {
        return $this->is_attribute_allowed === 1 ? true : false;
    }

    public static function createNew(
        string $mysqlFormat,
        string $phpFormat,
        bool $hasYear,
        bool $hasMonth,
        bool $hasDay,
        bool $isAttributeAllowed = false
    ): self
    {
        return static::create([
            'mysql_format' => $mysqlFormat,
            'php_format' => $phpFormat,
            'is_attribute_allowed' => $isAttributeAllowed,
            'has_year' => $hasYear === true ? 1 : 0,
            'has_month' => $hasMonth === true ? 1 : 0,
            'has_day' => $hasDay === true ? 1 : 0,
        ]);
    }
}