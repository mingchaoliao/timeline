<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:15 PM
 */

namespace App\Timeline\Domain\Repositories;


use App\Timeline\Domain\Collections\PeriodCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\Period;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Domain\ValueObjects\UserId;

interface PeriodRepository
{
    public function getTypeahead(): TypeaheadCollection;

    public function getAll(): PeriodCollection;

    public function create(string $value, UserId $createUserId): Period;

    public function bulkCreate(array $values, UserId $createUserId): PeriodCollection;

    public function update(PeriodId $id, string $value, UserId $updateUserId): Period;

    public function delete(PeriodId $id): bool;
}