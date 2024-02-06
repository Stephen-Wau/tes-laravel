<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = ['nama', 'email', 'phone'];

    public function hobbies(){
        return $this->hasMany(Hobby::class);
    }
}
