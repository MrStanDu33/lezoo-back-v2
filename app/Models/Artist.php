<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Event;

class Artist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'photo',
        'name',
        'social_link',
        'label',
    ];

    public function events() {
        return $this->belongsToMany(Event::class, 'artist_has_event', 'artist_id', 'event_id');
    }
}
