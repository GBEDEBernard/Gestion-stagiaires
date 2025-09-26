<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Service extends Model
{
use HasFactory;
 use SoftDeletes; // ✅ active le soft delete

protected $fillable = ['nom'];


public function stages()
{
return $this->hasMany(Stage::class);
}
}