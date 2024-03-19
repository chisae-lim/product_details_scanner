<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Variant
 * 
 * @property int $id_variant
 * @property int $id_product
 * @property int $id_color
 * 
 *
 * @package App\Models
 */
class Variant extends Model
{
	protected $table = 'tbl_variant';
	protected $primaryKey = 'id_variant';
	public $timestamps = false;

	protected $casts = [
		'id_product' => 'int',
		'id_color' => 'int'
	];

	protected $hidden = [
		'id_variant',
		'id_product',
		'id_color'
	];

	protected $fillable = [
		'id_product',
		'id_color'
	];

	public function id_product(): BelongsTo
	{
		return $this->belongsTo(Product::class, 'id_product');
	}

	public function id_color(): BelongsTo
	{
		return $this->belongsTo(Color::class, 'id_color');
	}
}
