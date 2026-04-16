<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnnouncementAsset extends Model
{
    protected $connection = 'announcement';
    protected $table = 'AnnouncementAssets';

    protected $fillable = [
        'announcement_id',
        'file_name',
        'file_path',
        'file_type',
        // other fields...
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'announcement_id', 'id');
    }
}
