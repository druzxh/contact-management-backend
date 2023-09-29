<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_code',
        'group_name',
        'group_description',
        'group_users_code',
        'isDelete',
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