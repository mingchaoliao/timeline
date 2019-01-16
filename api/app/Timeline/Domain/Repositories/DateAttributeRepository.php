<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:15 PM
 */

namespace App\Timeline\Domain\Repositories;


use App\Timeline\Domain\Collections\DateAttributeCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\DateAttribute;
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\UserId;

interface DateAttributeRepository
{
    public function getTypeahead(): TypeaheadCollection;

    public function getAll(): DateAttributeCollection;

    public function create(string $value, UserId $createUserId): DateAttribute;

    public function bulkCreate(array $values, UserId $createUserId): DateAttributeCollection;

    public function update(DateAttributeId $id, string $value, UserId $updateUserId): DateAttribute;

    public function delete(DateAttributeId $id): bool;
}