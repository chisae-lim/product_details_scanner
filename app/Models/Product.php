<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Product
 * 
 * @property int $id_product
 * @property string $p_code
 * @property string $bar_code
 * @property string $name_en
 * @property string $name_ch
 * @property float $price
 * @property string $description
 * @property int $length
 * @property int $width
 * @property int $height
 * @property int $id_scale
 * @property int $id_unit
 * @property int $id_category
 * @property int $id_brand
 * @property int $created_by
 * @property int $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Color[] $colors
 * @property Collection|ProductImage[] $images
 *
 * @package App\Models
 */
class Product extends Model
{
	protected $table = 'tbl_product';
	protected $primaryKey = 'id_product';

	protected $casts = [
		'price' => 'float',
		// 'length' => 'int',
		// 'width' => 'int',
		// 'height' => 'int',
		// 'id_scale' => 'int',
		'id_unit' => 'int',
		'id_category' => 'int',
		'id_brand' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $hidden = [
		'pivot',
		'id_scale',
		'id_unit',
		'id_category',
		'id_brand',
		'created_by',
		'updated_by'
	];

	protected $fillable = [
		'p_code',
		'bar_code',
		'name_en',
		'name_ch',
		'price',
		'description',
		// 'length',
		// 'width',
		// 'height',
		// 'id_scale',
		'id_unit',
		'id_category',
		'id_brand',
		'created_by',
		'updated_by'
	];

	public function category(): BelongsTo
	{
		return $this->belongsTo(Category::class, 'id_category');
	}

	public function created_by(): BelongsTo
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function brand(): BelongsTo
	{
		return $this->belongsTo(Brand::class, 'id_brand');
	}

	public function updated_by(): BelongsTo
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	// public function scale(): BelongsTo
	// {
	// 	return $this->belongsTo(Scale::class, 'id_scale');
	// }

	public function unit(): BelongsTo
	{
		return $this->belongsTo(Unit::class, 'id_unit');
	}

	public function colors(): BelongsToMany
	{
		return $this->belongsToMany(Color::class, 'tbl_variant', 'id_product', 'id_color')
			->withPivot('id_variant');
	}

	public function images(): HasMany
	{
		return $this->hasMany(ProductImage::class, 'id_product');
	}
}
