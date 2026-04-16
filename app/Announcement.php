<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $connection = 'announcement';
    protected $table = 'Announcements';

    protected $fillable = [
        'title',
        'content',
        'app',
        'targets',
        'expiration_date',
        'is_active',
        // other fields...
    ];

    protected $casts = [
        'targets' => 'array',
        'expiration_date' => 'datetime',
    ];

    public function views()
    {
        return $this->hasMany(AnnouncementView::class, 'announcement_id', 'id');
    }

    public function assets()
    {
        return $this->hasMany(AnnouncementAsset::class, 'announcement_id', 'id');
    }
}
