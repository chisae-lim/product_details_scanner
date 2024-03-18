<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class User
 *
 * @property int $id_user
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $auth_token
 * @property string|null $rem_token
 * @property string|null $rem_expiry
 * @property string $acc_status
 * @property string $act_status
 * @property int $created_by
 * @property int $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection|User[] $users_where_created_by
 * @property Collection|User[] $users_where_updated_by
 * @property Collection|Permission[] $permissions
 *
 * @package App\Models
 */
class User extends Model
{
    protected $table = 'tbl_user';
    protected $primaryKey = 'id_user';

    protected $casts = [
        'created_by' => 'int',
        'updated_by' => 'int',
    ];

    protected $hidden = [
		'pivot',
		'username',
		'password',
		'auth_token',
		'rem_token',
		'rem_expiry',
		'acc_status',
		'act_status',
		'created_by',
		'updated_by',
		'created_at',
		'updated_at'
	];

    protected $fillable = [
		'name',
		'username',
		'password',
		'auth_token',
		'rem_token',
		'rem_expiry',
		'acc_status',
		'act_status',
		'created_by',
		'updated_by'
	];

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function users_where_created_by(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function users_where_updated_by(): HasMany
    {
        return $this->hasMany(User::class, 'updated_by');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'tbl_user_permission', 'id_user', 'id_permission')
            ->withPivot('id_user_permission')
            ->orderBy('id_permission');
    }
}
