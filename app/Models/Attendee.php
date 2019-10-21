<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use App\Mail\SuccessfullyCreateAttendee;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Shipu\Watchable\Traits\WatchableTrait;

class Attendee extends Model
{
    use CrudTrait, WatchableTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'uuid',
        'type',
        'mobile',
        'email',
        'profession',
        'social_profile_url',
        'is_paid',
        'misc',
        'attend_at'
    ];

    protected $casts = [
        'misc' => 'array',
        'attend_at' => 'datetime'
    ];

    protected $appends = [
        'tshirt'
    ];

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getTshirtAttribute()
    {
        return array_get($this->misc, 'tshirt', 'N/A');
    }

    public function onModelCreating()
    {
        $this->uuid = DB::raw('UUID()');
    }

    public function onModelCreated()
    {
        dispatch(new SendEmailJob($this, new SuccessfullyCreateAttendee($this)));
    }
}
