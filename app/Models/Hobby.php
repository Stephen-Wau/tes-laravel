<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hobby extends Model
{
    protected $fillable = ['nama_hobby'];

    public function member(){
        return $this->belongsTo(Member::class);
    }
}
