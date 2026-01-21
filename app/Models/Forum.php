<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Forum extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'created_by',
    ];

    //Relation: who created this forum
    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    // Relation: channels inside this forum
    public function channels()
    {
        return $this->hasMany(Channel::class);
    }
}
