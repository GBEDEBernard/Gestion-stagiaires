<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Contact extends Model
{
     use SoftDeletes; // ✅ active le soft delete
    protected $fillable = [
        'name',
         'email',
          'subject',
           'message'];
}