<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Background
 * 
 * @property int $id_background
 * @property string $name
 *	@property string $status
 * @package App\Models
 */
class Background extends Model
{
	protected $table = 'tbl_background';
	protected $primaryKey = 'id_background';
	public $timestamps = false;
	protected $appends = [
		'image_url',
		'thumbnail_url'
	];
	protected $hidden = [

	];

	protected $fillable = [
		'name',
		'status'
	];

	protected function getHostUrl()
	{
		return request()->getScheme() . '://' . request()->getHost() . ':' . request()->getPort();
	}
	protected function getImageUrlAttribute()
	{
		return $this->getHostUrl() . '/assets/images/backgrounds/' . $this->name;
	}
	protected function getThumbNailUrlAttribute()
	{
		return $this->getHostUrl() . '/assets/images/backgrounds/thumbnails/' . $this->name;
	}
}
