<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
	use HasFactory;

	protected $fillable = [
		'conversation_id',
		'user_id',
		'body',
		'attachment_id'
	];

	/**
	 * Define las reglas de validación para los mensajes.
	 * El 'subject' es requerido solo para el mensaje inicial de una conversación.
	 *
	 * @param bool $isInitialMessage
	 * @return array
	 */
	public function getRulesCreate(bool $isInitialMessage = false)
	{
		$rules = [
			'body' => 'required|string',
		];

		if ($isInitialMessage) {
			$rules['subject'] = 'required|string|max:255';
			$rules['type'] = 'required|in:message,ticket';
			$rules['participants'] = 'required|array|min:1';
			$rules['participants.*'] = 'integer';
		}

		return $rules;
	}

	public function conversation(): BelongsTo
	{
		return $this->belongsTo(Conversation::class);
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function attachment(): BelongsTo
	{
		return $this->belongsTo(Attachment::class);
	}
}
