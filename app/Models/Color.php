<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Color
 * 
 * @property int $id_color
 * @property string $color
 * @property string $code
 * @property string $status
 * 
 * @property Collection|Product[] $products
 *
 * @package App\Models
 */
class Color extends Model
{
	protected $table = 'tbl_color';
	protected $primaryKey = 'id_color';
	public $timestamps = false;

	protected $hidden = [
		
	];

	protected $fillable = [
		'color',
		'code',
		'status'
	];

	public function products(): BelongsToMany
	{
		return $this->belongsToMany(Product::class, 'tbl_product_color', 'id_color', 'id_product')
					->withPivot('id_product_color');
	}
}
