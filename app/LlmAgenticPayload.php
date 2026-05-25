<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LlmAgenticPayload extends Model
{
    protected $connection = 'sales_intelligence';
    protected $table = 'llm_agentic_payload';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'is_used' => 'boolean',
        'is_expired' => 'boolean',
    ];
    public $timestamps = false;
}
