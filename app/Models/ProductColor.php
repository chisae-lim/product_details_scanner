<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProductColor
 * 
 * @property int $id_product_color
 * @property int $id_product
 * @property int $id_color
 * 
 *
 * @package App\Models
 */
class ProductColor extends Model
{
	protected $table = 'tbl_product_color';
	protected $primaryKey = 'id_product_color';
	public $timestamps = false;

	protected $casts = [
		'id_product' => 'int',
		'id_color' => 'int'
	];

	protected $hidden = [
		'id_product',
		'id_color'
	];

	protected $fillable = [
		'id_product',
		'id_color'
	];

	public function product(): BelongsTo
	{
		return $this->belongsTo(Product::class, 'id_product');
	}

	public function color(): BelongsTo
	{
		return $this->belongsTo(Color::class, 'id_color');
	}
}
