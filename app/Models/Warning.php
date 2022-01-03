<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;

    protected $fillable = [
        'event',
        'lvl',
        'messtype',
        'starttime',
        'endtime',
        'prob',
        'how',
        'canceltime',
        'cause',
        'sms',
        'rso',
        'remarks',
        'file',
        'downloaded_at',
    ];
}
