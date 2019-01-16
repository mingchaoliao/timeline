<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 5:09 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EloquentImage extends Model
{
    protected $table = 'images';
    protected $guarded = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getEventId(): int
    {
        return $this->event_id;
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
}