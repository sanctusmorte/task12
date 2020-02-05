<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersNote extends Model
{
    protected $table = 'users_notes';

    public $timestamps = false;

    protected $fillable = ['user_id'];
}
