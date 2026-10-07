<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class SaleModel extends Model
{
    protected $table = 'sales';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'seller_id',
        'seller_username',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItemModel::class, 'sale_id', 'id');
    }
}