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
        'is_delete',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'contact_users_code', 'users_code');
    }

    public function social_contacts()
    {
        return $this->hasMany(SocialContact::class, 'social_contact_code', 'contact_code');
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'contact_group', 'contact_id', 'group_id');
    }
}