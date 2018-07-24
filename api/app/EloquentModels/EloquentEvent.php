<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 5:09 PM
 */

namespace App\EloquentModels;


use App\DomainModels\Image;
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

    public function start_date_format(): BelongsTo
    {
        return $this->belongsTo(
            EloquentDateFormat::class,
            'start_date_format_id',
            'id'
        );
    }

    public function end_date_format(): BelongsTo
    {
        return $this->belongsTo(
            EloquentDateFormat::class,
            'end_date_format_id',
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
     * @return Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->start_date;
    }

    /**
     * @return Carbon|null
     */
    public function getEndDate(): ?Carbon
    {
        return $this->end_date;
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

    public function getStartDateFormat(): EloquentDateFormat
    {
        return $this->start_date_format;
    }

    public function getEndDateFormat(): ?EloquentDateFormat
    {
        return $this->end_date_format;
    }

    public function scopeByIds(Builder $query, array $ids)
    {
        $query->whereIn('id', $ids);
    }

    public static function createNew(
        Carbon $startDate,
        string $content,
        int $createUserId,
        int $startDateFormatId,
        int $endDateFormatId = null,
        int $startDateAttributeId = null,
        Carbon $endDate = null,
        int $endDateAttributeId = null,
        int $periodId = null,
        array $catalogIds = [],
        array $imageData = []
    ): EloquentEvent {
        /**
         * @var EloquentEvent $eloquentEvent
         * */
        $eloquentEvent = static::create([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_date_attribute_id' => $startDateAttributeId,
            'start_date_format_id' => $startDateFormatId,
            'end_date_format_id' => $endDateFormatId,
            'end_date_attribute_id' => $endDateAttributeId,
            'content' => $content,
            'period_id' => $periodId,
            'create_user_id' => $createUserId
        ]);

        $images = [];
        foreach ($imageData as $image) {
            $images[] = new EloquentImage([
                'path' => $image['path'],
                'description' => $image['description'],
                'create_user_id' => $createUserId
            ]);
        }
        $eloquentEvent->images()->saveMany($images);

        foreach ($imageData as $image) {
            Storage::disk()->move(
                Image::TMP_PATH . '/' . $image['path'],
                Image::PATH . '/' . $image['path']
            );
        }

        $eloquentEvent->catalogs()->attach($catalogIds);

        return $eloquentEvent;
    }

    public static function updateById(
        int $id,
        Carbon $startDate,
        string $content,
        int $updateUserId,
        int $startDateFormatId,
        int $endDateFormatId = null,
        int $startDateAttributeId = null,
        Carbon $endDate = null,
        int $endDateAttributeId = null,
        int $periodId = null,
        array $catalogIds = [],
        array $imageData = []
    ): EloquentEvent {
        /**
         * @var EloquentEvent $event
         * */
        $event = static::findOrFail($id);

        $event->update([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_date_attribute_id' => $startDateAttributeId,
            'start_date_format_id' => $startDateFormatId,
            'end_date_format_id' => $endDateFormatId,
            'end_date_attribute_id' => $endDateAttributeId,
            'content' => $content,
            'period_id' => $periodId,
            'update_user_id' => $updateUserId
        ]);

        $images = [];
        foreach ($imageData as $image) {
            $images[] = new EloquentImage([
                'path' => $image['path'],
                'description' => $image['description'],
                'create_user_id' => $updateUserId
            ]);
        }
        $event->images()->delete();
        $event->images()->saveMany($images);

        foreach ($imageData as $image) {
            if(Storage::exists(Image::TMP_PATH . '/' . $image['path'])) {
                Storage::disk()->move(
                    Image::TMP_PATH . '/' . $image['path'],
                    Image::PATH . '/' . $image['path']
                );
            }
        }

        $event->catalogs()->sync($catalogIds);

        return $event;
    }
}