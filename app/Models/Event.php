<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Artist;
use App\Models\Style;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'title',
        'description',
    ];

    public function artists() {
        return $this->belongsToMany(Artists::class, 'artist_has_event', 'event_id', 'artist_id');
    }

    public function styles() {
        return $this->belongsToMany(Style::class, 'event_has_style', 'event_id', 'style_id');
    }
}
