<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionType extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'color', 'description', 'fields_config', 'active', 'sort_order'];

    protected $casts = [
        'fields_config' => 'array',
        'active'        => 'boolean',
    ];

    public function requests()
    {
        return $this->hasMany(PermissionRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('sort_order');
    }

    public function colorClass(): string
    {
        return match($this->color) {
            'red'    => 'text-red-400 bg-red-500/10 border-red-500/20',
            'amber'  => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
            'orange' => 'text-orange-400 bg-orange-500/10 border-orange-500/20',
            'purple' => 'text-purple-400 bg-purple-500/10 border-purple-500/20',
            'green'  => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
            'blue'   => 'text-blue-400 bg-blue-500/10 border-blue-500/20',
            default  => 'text-slate-400 bg-slate-500/10 border-slate-500/20',
        };
    }

    public function badgeClass(): string
    {
        return match($this->color) {
            'red'    => 'bg-red-100 text-red-700',
            'amber'  => 'bg-amber-100 text-amber-700',
            'orange' => 'bg-orange-100 text-orange-700',
            'purple' => 'bg-purple-100 text-purple-700',
            'green'  => 'bg-emerald-100 text-emerald-700',
            'blue'   => 'bg-blue-100 text-blue-700',
            default  => 'bg-slate-100 text-slate-700',
        };
    }
}
