<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AlbumNew extends Authenticatable
{
    use Notifiable;

    protected $connection = "mysql";
    protected $table="albums";
}
