<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AudioNew extends Authenticatable
{
    use Notifiable;
    protected $connection = "mysql";
    protected $table="audios_audios";
}
