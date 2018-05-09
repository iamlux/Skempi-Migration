<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AudioTag extends Authenticatable
{
    use Notifiable;
    protected $connection = "mysql";
    protected $table="audio_tags";
    public $timestamps = false;	
}
