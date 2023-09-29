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
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'contact_group_code', 'group_code');
    }

    public function getGroupTotalAttribute()
    {
        return $this->contacts()->count();
    }

}