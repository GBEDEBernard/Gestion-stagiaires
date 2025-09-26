<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jour extends Model
{
    use HasFactory;
     use SoftDeletes; // ✅ active le soft delete
    protected $fillable = ['jour'];

    // Relation : un jour peut avoir plusieurs stages
    public function stages()
    {
        return $this->hasMany(Stage::class, 'jour_id'); 
    }
}
