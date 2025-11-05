<?php

namespace App\Models;

use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWalletFloat;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Notifications\Notification;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use TomatoPHP\FilamentAccounts\Models\AccountRequest;
use TomatoPHP\FilamentAccounts\Traits\InteractsWithTenant;
use TomatoPHP\FilamentAlerts\Traits\InteractsWithNotifications;
use TomatoPHP\FilamentCms\Models\Comment;
use TomatoPHP\FilamentCms\Models\Post;
use TomatoPHP\FilamentEmployees\Traits\IsEmployee;
use TomatoPHP\FilamentFcm\Traits\InteractsWithFCM;
use TomatoPHP\FilamentInvoices\Traits\BilledFor;
use TomatoPHP\FilamentLocations\Models\Location;

/**
 * @property int            $id
 * @property string         $name
 * @property string         $username
 * @property string         $loginBy
 * @property string         $type
 * @property string         $address
 * @property string         $password
 * @property string         $otp_code
 * @property string         $otp_activated_at
 * @property string         $last_login
 * @property string         $agent
 * @property string         $host
 * @property int            $attempts
 * @property bool           $login
 * @property bool           $activated
 * @property bool           $blocked
 * @property string         $deleted_at
 * @property string         $created_at
 * @property string         $updated_at
 * @property AccountsMeta[] $accountsMetas
 * @property Model meta($key, $value)
 * @property Location[] $locations
 */
class Account extends Authenticatable implements HasMedia, HasAvatar, HasTenants, Wallet, FilamentUser
{
    use BilledFor;
    use HasApiTokens;
    use HasFactory;
    use HasWalletFloat;
    use InteractsWithFCM;
    use InteractsWithMedia;
    use InteractsWithNotifications;
    use InteractsWithTenant;
    use IsEmployee;
    use Notifiable;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'email',
        'phone',
        'parent_id',
        'type',
        'name',
        'username',
        'loginBy',
        'address',
        'lang',
        'password',
        'otp_code',
        'otp_activated_at',
        'last_login',
        'agent',
        'host',
        'is_login',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_login'  => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
        'otp_activated_at',
        'last_login',
    ];

    protected $appends = [
        'birthday',
        'gender',
        'is_public',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
        'otp_activated_at',
        'host',
        'agent',
    ];

    /**
     * @return string|null
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return  $this->getFirstMediaUrl('avatar') ?? null;
    }

    public function getIsPublicAttribute(): Model|string|bool|null
    {
        return $this->meta('is_public') ?: null;
    }

    /**
     * @return Model|string|null
     */
    public function getBirthdayAttribute(): Model|string|null
    {
        return $this->meta('birthday') ?: null;
    }

    /**
     * @return Model|string|null
     */
    public function getGenderAttribute(): Model|string|null
    {
        return $this->meta('gender') ?: null;
    }

    /**
     * @return HasMany
     */
    public function accountsMetas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('TomatoPHP\FilamentAccounts\Models\AccountsMeta');
    }

    /**
     * @param string                   $key
     * @param string|array|object|null $value
     *
     * @return Model|string|array|null
     */
    public function meta(string $key, string|array|object|null $value = null): Model|string|array|null
    {
        if ($value !== null) {
            if ($value === 'null') {
                return $this->accountsMetas()->updateOrCreate(['key' => $key], ['value' => null]);
            }

            return $this->accountsMetas()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $meta = $this->accountsMetas()->where('key', $key)->first();
        if ($meta) {
            return $meta->value;
        }

        return $this->accountsMetas()->updateOrCreate(['key' => $key], ['value' => null]);
    }

    /**
     * @return MorphMany
     */
    public function locations(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Location::class, 'modelbale', 'model_type', 'model_id');
    }

    /**
     * @return HasMany
     */
    public function requests(): HasMany
    {
        return $this->hasMany(AccountRequest::class, 'account_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'account_id', 'id');
    }

    public function like(Post $post)
    {
        $exists = $this->likes()->where('post_id', $post->id)->first();

        if ( ! $exists) {
            $this->likes()->create(['post_id' => $post->id]);
            $post->likes += 1;
            $post->save();

            $this->log($post, 'like', 'liked post');
            if ($post->author) {
                Notification::make()
                    ->title('New Like')
                    ->body("{$this->name} liked your post.")
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('viewComment')
                            ->label('View Comment')
                            ->url(url('/admin/posts/' . $post->id . '/show')),
                    ])
                    ->success()
                    ->sendToDatabase($post->author);
            }
        } else {
            $exists->delete();

            $post->likes -= 1;
            $post->save();

            if ($post->author) {
                Notification::make()
                    ->title('New Dislike')
                    ->body("{$this->name} disliked your post.")
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('viewComment')
                            ->label('View Comment')
                            ->url(url('/admin/posts/' . $post->id . '/show')),
                    ])
                    ->success()
                    ->sendToDatabase($post->author);
            }
        }
    }

    /**
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'user');
    }

    /**
     * @param Post $post
     *
     * @return bool
     */
    public function isLike(Post $post): bool
    {
        return (bool) $this->likes()->where('post_id', $post->id)->first();
    }

    /**
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AccountLog::class);
    }

    /**
     * @param Model       $model
     * @param string      $action
     * @param string|null $log
     * @param string|null $date
     *
     * @return Model
     */
    public function log(Model $model, string $action = 'comment', ?string $log = null, ?string $date = null): Model
    {
        $data = [
            'action'     => $action,
            'log'        => $log,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model ? $model->id : null,
        ];

        if ($date) {
            $data['created_at'] = $date;
        }

        return $this->logs()->create($data);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return true;
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return [];
    }
}
