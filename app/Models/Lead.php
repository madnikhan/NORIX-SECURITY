<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['company', 'contact_name', 'email', 'phone', 'service_interest', 'message', 'status'])]
class Lead extends Model
{
    //
}
