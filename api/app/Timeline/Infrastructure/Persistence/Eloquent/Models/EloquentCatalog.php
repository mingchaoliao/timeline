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

class EloquentCatalog extends Model
{
    protected $table = 'catalogs';
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

    public function scopeWithFullInfo(Builder $query) {
        $query->with(['create_user', 'update_user']);
    }

    public function create_user(): BelongsTo {
        return $this->belongsTo(EloquentUser::class,'create_user_id', 'id');
    }

    public function update_user(): BelongsTo {
        return $this->belongsTo(EloquentUser::class,'update_user_id', 'id');
    }

    public function getNumberOfEventsAttribute() {
        return $this->belongsToMany(EloquentEvent::class, 'catalog_event','catalog_id', 'event_id')->count();
    }

    public function getNumberOfEvents(): int {
        return $this->number_of_events;
    }

    public function getCreateUser(): ?EloquentUser {
        return $this->create_user;
    }

    public function getUpdateUser(): ?EloquentUser {
        return $this->update_user;
    }

    public function scopeByValues(Builder $query, array $values): Builder {
        return $query->whereIn('value', $values);
    }
}