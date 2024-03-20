<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProductImage
 * 
 * @property int $id_product_image
 * @property int $id_product
 * @property string $name
 * 
 *
 * @package App\Models
 */
class ProductImage extends Model
{
	protected $table = 'tbl_product_image';
	protected $primaryKey = 'id_product_image';
	public $timestamps = false;
	protected $appends = [
		'image_url',
		'thumbnail_url'
	];
	protected $casts = [
		'id_product' => 'int'
	];

	protected $hidden = [
		'id_product'
	];

	protected $fillable = [
		'id_product',
		'name'
	];
	protected function getHostUrl()
	{
		return request()->getScheme() . '://' . request()->getHost() . ':' . request()->getPort();
	}
	protected function getImageUrlAttribute()
	{
		return $this->getHostUrl() . '/assets/images/products/' . $this->name;
	}
	protected function getThumbNailUrlAttribute()
	{
		return $this->getHostUrl() . '/assets/images/products/thumbnails/' . $this->name;
	}
	public function product(): BelongsTo
	{
		return $this->belongsTo(Product::class, 'id_product');
	}
}
