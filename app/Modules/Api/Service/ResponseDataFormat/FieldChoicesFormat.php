<?php


namespace App\Modules\Api\Service\ResponseDataFormat;


use Illuminate\Support\Collection;

class FieldChoicesFormat extends AbstractFormat
{
    /**
     * @param Collection $collection
     * @return Collection
     */
    public static function format(Collection $collection)
    {

        $collection->each(function($el){

            if  (!empty($el->choices)){
                $fieldChoiceCollection = new Collection();
               // dd($el->choices);
                foreach($el->choices as $key=>$choice){
                    if(isset($choice)){
                        $choiceObject = new FieldChoices();
                        $choiceObject->setDefaultValue($choice);
                        $choiceObject->setMeta($el->dictionary_meta[$key]??null);
                        $choiceObject->setValue('');
                        $choiceObject->setId($el->dictionary_id[$key]);
                        $choiceObject->setDictionaryOrder($el->dictionary_order[$key]);
                        $fieldChoiceCollection->push($choiceObject);
                    }

                }
                $el->choices = $fieldChoiceCollection->toArray();
            }
            unset($el->dictionary_id);
            unset($el->dictionary_order);
            unset($el->dictionary_meta);
        });

        return $collection;
    }
}
