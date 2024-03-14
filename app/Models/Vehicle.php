<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'type_vehicle_id',
        'brand_vehicle_id',
        'model_vehicle_id',
        'color_vehicle_id',
        'plate',
        'year',
        'odo',
        'note',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function type()
    {
        return $this->belongsTo(TypeVehicle::class, 'type_vehicle_id');
    }

    public function brand()
    {
        return $this->belongsTo(BrandVehicle::class, 'brand_vehicle_id');
    }

    public function model()
    {
        return $this->belongsTo(ModelVehicle::class, 'model_vehicle_id');
    }

    public function color()
    {
        return $this->belongsTo(ColorVehicle::class, 'color_vehicle_id');
    }
}
