<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'key',
        'value',
        'description',
        'type',
        'created_by',
        'updated_by'
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data native.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Boot metode untuk model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->created_by) && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    /**
     * Mendapatkan nilai setting berdasarkan kunci.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        // Parse nilai berdasarkan tipe data
        switch ($setting->type) {
            case 'integer':
                return (int) $setting->value;
            case 'float':
                return (float) $setting->value;
            case 'boolean':
                return in_array(strtolower($setting->value), ['true', '1', 1]);
            case 'array':
            case 'json':
                return json_decode($setting->value, true);
            default:
                return $setting->value;
        }
    }

    /**
     * Set nilai setting berdasarkan kunci.
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $type
     * @return bool
     */
    public static function setValue($key, $value, $type = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return false;
        }

        if ($type) {
            $setting->type = $type;
        }

        // Format nilai berdasarkan tipe data
        switch ($setting->type) {
            case 'string':
                $setting->value = (string) $value;
                break;
            case 'integer':
                $setting->value = (int) $value;
                break;
            case 'float':
                $setting->value = (float) $value;
                break;
            case 'boolean':
                $setting->value = $value ? 'true' : 'false';
                break;
            case 'array':
            case 'json':
                $setting->value = is_string($value) ? $value : json_encode($value);
                break;
            default:
                $setting->value = $value;
        }

        return $setting->save();
    }

    /**
     * Relasi dengan pengguna yang membuat setting.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi dengan pengguna yang terakhir memperbarui setting.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}