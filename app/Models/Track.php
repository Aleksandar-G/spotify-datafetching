<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    public function store(Request $request)
    {
        // Validate the request...

        $track = new Track;

        $track->name = $request->name;

        $track->save();
    }
}
