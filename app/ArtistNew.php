<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ArtistNew extends Authenticatable
{
    use Notifiable;
    protected $connection = "mysql";
    protected $table="artists";
}
