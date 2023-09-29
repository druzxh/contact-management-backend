<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = [
        'contact_code',
        'name',
        'email',
        'phone',
        'company',
        'contact_social_code',
        'contact_users_code',
        'contact_group_code',
        'isDelete',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'contact_users_code', 'users_code');
    }

    public function social()
    {
        return $this->belongsTo(Social::class, 'contact_social_code', 'social_code');
    }
    public function groups()
    {
        return $this->belongsToMany(GroupContact::class, 'contact_group', 'contact_id', 'group_id');
    }
}