<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Scale
 * 
 * @property int $id_scale
 * @property string $name
 * @property string $label
 * 
 * @property Collection|Product[] $products
 *
 * @package App\Models
 */
class Scale extends Model
{
	protected $table = 'tbl_scale';
	protected $primaryKey = 'id_scale';
	public $timestamps = false;

	protected $hidden = [
		
	];

	protected $fillable = [
		'name',
		'label'
	];

	public function products(): HasMany
	{
		return $this->hasMany(Product::class, 'id_scale');
	}
}
