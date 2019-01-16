<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 5:09 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class EloquentDateAttribute extends Model
{
    protected $table = 'date_attributes';
    protected $guarded = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCreateUserId(): int
    {
        return $this->create_user_id;
    }

    public function getUpdateUserId(): ?int
    {
        return $this->update_user_id;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->updated_at;
    }

    public function create_user(): BelongsTo {
        return $this->belongsTo(EloquentUser::class,'create_user_id', 'id');
    }

    public function update_user(): BelongsTo {
        return $this->belongsTo(EloquentUser::class,'update_user_id', 'id');
    }

    public function getCreateUser(): ?EloquentUser {
        return $this->create_user;
    }

    public function getUpdateUser(): ?EloquentUser {
        return $this->update_user;
    }

    public static function createNew(
        string $value,
        int $createUserId
    ): self {
        return static::create([
            'value' => $value,
            'create_user_id' => $createUserId
        ]);
    }

    public function scopeByValues(Builder $query, array $values): Builder
    {
        return $query->whereIn('value', $values);
    }

    public static function bulkCreate(array $values, int $createUserId): Collection
    {
        $collection = new Collection();

        foreach ($values as $value) {
            $collection->push(self::createNew($value, $createUserId));
        }

        return $collection;
    }
}