<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialContact extends Model
{
    use HasFactory;
    protected $fillable = [
        'social_code',
        'social_name',
        'social_url',
        'social_is_delete'
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'social_contact_code', 'contact_code');
    }
}