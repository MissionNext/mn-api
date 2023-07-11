<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 04.10.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Models\My;

use Illuminate\Database\Eloquent\Model;

class FieldType extends Model
{
    public const DATE = 1,
        SELECT = 2,
        INPUT = 3,
        SELECT_MULTIPLE = 4,
        TEXT = 5,
        RADIO = 6,
        BOOLEAN = 7,
        CHECKBOX = 8,
        FILE = 9,
        RADIO_YES_NO = 10,
        MARITAL_STATUS = 11;

    private static $multipleTypes = [
        FieldType::SELECT_MULTIPLE,
        FieldType::CHECKBOX
    ];

    private static $dictionaryTypes = [
        self::SELECT,
        self::SELECT_MULTIPLE,
        self::RADIO,
        self::CHECKBOX
    ];

    private static $inputTypes = [
        self::DATE,
        self::INPUT,
        self::TEXT,
        self::FILE
    ];

    public $timestamps = false;

    protected $table = 'field_types';

    protected $fillable =[
        'name'
    ];

    public static function isMultiple($typeId): bool
    {
        return in_array($typeId, static::$multipleTypes, true);
    }

    public static function hasDictionary($typeId): bool
    {
        return in_array($typeId, static::$dictionaryTypes, true);
    }

    public static function hasInputTypes($typeId): bool
    {

        return in_array($typeId, static::$inputTypes, true);
    }

    public function candidateFields()
    {

        return $this->hasMany(Candidate::class, 'type');
    }
}
