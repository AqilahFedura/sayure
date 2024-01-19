<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Food extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'ingredients', 'price', 'rate', 'types',
        'picurepath'
    ];

    public function getCreatedAtAttribure($value)
    {
        return Carbon::parse($value)->timestamp;
    }
    public function getUpdatedAttribure($value)
    {
        return Carbon::parse($value)->timestamp;
    }

    public function toArray()
    {
        $toArray = parent::toArray();
        $toArray =['picurepath'] = $this->picurepath;
        return $toArray;
    }

    public function getPicurePathAttribute()
    {
        return url('') . Storage::url($this->attributes['picurepath']);
    }

    //jadi laravel itu gabisa baca database yang kayak "yamgoreng" harusnya "yam_goreng". maka dari 
    //itu diakali kayak gini, sehingga nanti laravelnya baca picurepath bukan picure_path
}
