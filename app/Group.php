<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
    ];

    public function posts(){
        return $this->hasMany('App\Post');
    }

    public function users(){
        return $this->belongsToMany('App\User');
    }
}
