<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 5:09 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Models;


use App\Timeline\Domain\Models\Image;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class EloquentEvent extends Model
{
    protected $table = 'events';
    protected $guarded = [];

    protected $dates = [
        'start_date',
        'end_date'
    ];

    public function start_date_attribute(): BelongsTo
    {
        return $this->belongsTo(
            EloquentDateAttribute::class,
            'start_date_attribute_id',
            'id'
        );
    }

    public function end_date_attribute(): BelongsTo
    {
        return $this->belongsTo(EloquentDateAttribute::class,
            'end_date_attribute_id',
            'id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(
            EloquentPeriod::class,
            'period_id',
            'id'
        );
    }

    public function catalogs(): BelongsToMany
    {
        return $this->belongsToMany(
            EloquentCatalog::class,
            'catalog_event',
            'event_id',
            'catalog_id',
            'id',
            'id'
        );
    }

    public function images(): HasMany
    {
        return $this->hasMany(
            EloquentImage::class,
            'event_id',
            'id'
        );
    }

    public function scopeOrderByStartDate(Builder $query)
    {
        $query->orderBy('start_date');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStartDateStr(): string
    {
        return $this->start_date_str;
    }

    /**
     * @return string|null
     */
    public function getEndDateStr(): ?string
    {
        return $this->end_date_str;
    }

    public function getStartDateAttributeObj(): ?EloquentDateAttribute
    {
        return $this->start_date_attribute;
    }

    public function getEndDateAttributeObj(): ?EloquentDateAttribute
    {
        return $this->end_date_attribute;
    }

    public function getPeriod(): ?EloquentPeriod
    {
        return $this->period;
    }

    public function getCatalogCollection(): Collection
    {
        return $this->catalogs;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getImageCollection(): Collection
    {
        return $this->images;
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

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at;
    }

    public function scopeByIds(Builder $query, array $ids)
    {
        $query->whereIn('id', $ids);
    }
}