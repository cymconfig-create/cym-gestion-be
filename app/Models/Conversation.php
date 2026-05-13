<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $table = 'conversations';
    protected $primaryKey = 'conversation_id';

    protected $fillable = [
        'subject',
        'type',
        'created_by',
        'last_message_at',
        'status',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id')->latest('created_at');
    }

    public function users(): BelongsToMany
    {
        // Se especifican las claves foráneas en la relación de muchos a muchos.
        // El tercer parámetro es la clave foránea del modelo que contiene esta relación (Conversation).
        // El cuarto parámetro es la clave foránea del modelo relacionado (User).
        return $this->belongsToMany(User::class, 'conversation_user', 'conversation_id', 'user_id')
            ->withPivot('is_archived', 'is_deleted', 'last_read_at');
    }
}