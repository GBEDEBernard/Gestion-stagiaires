<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Activity extends Model
{
    use HasFactory;
     use SoftDeletes; // ✅ active le soft delete
    protected $fillable = ['user_id', 'action', 'description'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

