<?php

namespace App\Models;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domaine extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['nom', 'description', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'domaine_id');
    }

    public function stages()
    {
        return $this->hasMany(Stage::class, 'domaine_id');
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'domaine_site');
    }
}
