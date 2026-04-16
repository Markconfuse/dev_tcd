<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnnouncementView extends Model
{
    protected $connection = 'announcement';
    protected $table = 'AnnouncementViews';
    public $timestamps = false;

    protected $fillable = [
        'announcement_id',
        'account_id',
        'viewed_at'
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'announcement_id', 'id');
    }
}
