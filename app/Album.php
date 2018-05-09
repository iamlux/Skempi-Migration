<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Album extends Authenticatable
{
    use Notifiable;

    protected $table="core_album";
}
