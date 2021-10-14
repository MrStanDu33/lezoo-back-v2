<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Event;
use App\Models\Resident;

class Photo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'alt',
    ];

    public function album() {
        return $this->belongsTo(Album::class);
    }
    public function artist() {
        return $this->belongsTo(Artist::class);

    }
    public function event() {
        return $this->belongsTo(Event::class);

    }
    public function resident() {
        return $this->belongsTo(Resident::class);

    }
}
