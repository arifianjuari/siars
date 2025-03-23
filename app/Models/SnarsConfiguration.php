<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsConfiguration extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_configurations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_system',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Creator relationship
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Updater relationship
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get typed value based on configuration type
     */
    public function getTypedValueAttribute()
    {
        switch ($this->type) {
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            case 'boolean':
                return (bool) $this->value;
            case 'date':
                return Carbon::parse($this->value);
            case 'json':
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    /**
     * Get configuration value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $config = self::where('key', $key)->first();

        if (!$config) {
            return $default;
        }

        return $config->typed_value;
    }

    /**
     * Set configuration value by key
     */
    public static function setValue(string $key, $value, int $updatedBy = null)
    {
        $config = self::where('key', $key)->first();

        if (!$config) {
            return false;
        }

        $config->value = $value;

        if ($updatedBy) {
            $config->updated_by = $updatedBy;
        }

        return $config->save();
    }
}
