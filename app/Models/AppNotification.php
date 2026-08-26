<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $fillable = [
        'unique_id',
        'type',
        'is_urgent',
        'target_type',
        'target_value',
        'batch_id',
        'sender_id',
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
        'read_at'   => 'datetime',
        'is_urgent' => 'boolean',
    ];

    // Relation avec l'utilisateur destinataire
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec l'émetteur (admin)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Scope pour les notifications urgentes
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    // Scope pour la notification urgente non lue active d'un utilisateur
    public function scopeUrgentUnreadForUser($query, $userId)
    {
        $id = $userId instanceof User ? $userId->id : $userId;
        return $query->where('user_id', $id)
                     ->where('is_urgent', true)
                     ->whereNull('read_at');
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

    // Scope pour cacher les notifications admin aux utilisateurs non-admin
    public function scopeVisibleForUser($query, $user)
    {
        $query->where('user_id', $user->id);

        if (!$user->hasAnyRole(['admin', 'superviseur'])) {
            $query->whereNotIn('type', [
                'nouveau_etudiant',
                'stage_fin_semaine',
                'stage_termine',
                'presence_anomalies',
                'rapports_en_attente',
            ]);
        }

        return $query;
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

    // Vérifier si urgente
    public function isUrgent(): bool
    {
        return (bool) $this->is_urgent;
    }
}

