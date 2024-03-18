<?php
namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait TimeStamps
{
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->timezone(config('app.timezone'))->format('d-m-Y h:i:s A'),
        );
    }
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->timezone(config('app.timezone'))->format('d-m-Y h:i:s A'),
        );
    }
}
