<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_code',
        'group_name',
        'group_description',
        'group_users_code',
        'is_delete',
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_group', 'group_id', 'contact_id');
    }

    public function getGroupTotalAttribute()
    {
        return $this->contacts()->count();
    }

}