<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $fillable = [
        'unique_id',
        'type',
        'title',
        'message',
        'icon',
        'color',
        'reference_id',
        'reference_type',
        'url',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope pour les notifications non lues
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Scope pour les notifications d'un utilisateur
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Marquer comme lu
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    // Vérifier si lu
    public function isRead()
    {
        return $this->read_at !== null;
    }
}
