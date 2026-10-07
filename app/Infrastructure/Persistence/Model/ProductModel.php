<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Model;

use Illuminate\Database\Eloquent\Model;

final class ProductModel extends Model
{
    protected $table = 'products';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'price',
        'currency',
        'stock',
        'category_id',
        'image_url',
        'version',
        'deleted_at'
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'version' => 'integer',
        'deleted_at' => 'datetime',
    ];
}