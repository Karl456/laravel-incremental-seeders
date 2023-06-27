<?php

namespace Karl456\IncrementalSeeders\Models;

use Illuminate\Database\Eloquent\Model;

class IncrementalSeeder extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'seeder',
    ];
}
