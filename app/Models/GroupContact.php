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
        'group_users_code'
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_group', 'group_code', 'contact_code');
    }

    public function getGroupTotalAttribute()
    {
        return $this->contacts()->count();
    }

}