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
        'group_total',
        'group_description',
    ];
}