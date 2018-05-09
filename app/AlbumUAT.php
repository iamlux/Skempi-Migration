<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AlbumUAT extends Authenticatable
{
    use Notifiable;

    protected $connection = "mysql";
    protected $table="albums_uat";
}
