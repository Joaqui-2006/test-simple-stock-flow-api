<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Model;

use Illuminate\Database\Eloquent\Model;

final class SaleItemModel extends Model
{
    protected $table = 'sale_items';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'sale_id',
        'product_id',
        'product_name',
        'unit_price',
        'currency',
        'quantity'
    ];

    protected $casts = [
        'unit_price' => 'float',
        'quantity' => 'integer',
    ];
}