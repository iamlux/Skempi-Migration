<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AudioUAT extends Authenticatable
{
    use Notifiable;
    protected $connection = "mysql";
    protected $table="audios_uat";
}
