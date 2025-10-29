<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Tambahkan import ini

class Device extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'mqtt_username',
    ];

    /**
     * Mendapatkan user yang memiliki device ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
