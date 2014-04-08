<?php
namespace MissionNext\Models\Field;

use Illuminate\Database\Eloquent\Model as Eloquent;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\Field\Candidate as CandidateModel;

class FieldType extends Eloquent implements ModelInterface
{
    const DATE = 1,
          SELECT = 2,
          INPUT = 3,
          SELECT_MULTIPLE = 4,
          TEXT  = 5,
          RADIO = 6,
          BOOLEAN = 7,
          CHECKBOX = 8;

    private static $multipleTypes = [FieldType::SELECT_MULTIPLE, FieldType::CHECKBOX ];

    private static $dictionaryTypes = [ self::SELECT, self::SELECT_MULTIPLE, self::RADIO, self::CHECKBOX ];

    public static function isMultiple($typeId){

        return in_array($typeId , static::$multipleTypes);
    }

    public static function hasDictionary($typeId){

        return in_array($typeId, static::$dictionaryTypes);
    }

    public $timestamps = false;

    protected $table = 'field_types';

    protected $fillable = array('name');

    public function candidateFields()
    {

        return $this->hasMany(CandidateModel::class, 'type');
    }

} 