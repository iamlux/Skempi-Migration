<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Audio extends Authenticatable
{
    use Notifiable;

    protected $table="core_song";
}
