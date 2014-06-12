<?php

namespace MissionNext\Repos\Form;


use Illuminate\Support\Facades\DB;
use MissionNext\DB\SqlStatement\Sql;
use MissionNext\Models\Form\AppForm;
use MissionNext\Repos\AbstractRepository;
use MissionNext\Repos\Field\FieldDataTransformer;
use MissionNext\Repos\Field\FieldToArrayTransformStrategy;

class FormRepository extends AbstractRepository implements FormRepositoryInterface
{
    protected $modelClassName = AppForm::class;

    /**
     * @return AppForm
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @param AppForm $form
     *
     * @return FieldDataTransformer
     */
    public function groupedFields(AppForm $form)
    {
        $type = $this->repoContainer->securityContext()->role();
        $language = $this->repoContainer->securityContext()->getToken()->language();
        $dm = $this->repoContainer->securityContext()->getApp()->DM();

        $builder = $form
            ->leftJoin("form_groups", "form_groups.form_id","=", "app_forms.id")
            ->leftJoin("group_fields", "group_fields.group_id", "=", "form_groups.id")
            ->leftJoin($type."_fields", "group_fields.symbol_key", "=", $type."_fields.symbol_key")
            ->leftJoin('data_model_' . $type . '_fields', $type . '_fields.id', '=', 'data_model_' . $type . '_fields.field_id')
            ->leftJoin('field_types', 'field_types.id', '=', $type . '_fields.type')
            ->leftJoin($type . '_dictionary', $type . '_dictionary.field_id', '=', $type . '_fields.id')
            ->leftJoin($type . '_fields_trans', function ($join) use ($type, $language ) {
                $join->on($type . '_fields_trans.field_id', '=', $type . '_fields.id')
                    ->where($type . '_fields_trans.lang_id', '=', $language->id);
            })
            ->leftJoin($type . '_dictionary_trans', function ($join) use ($type, $language) {
                $join->on($type . '_dictionary_trans.dictionary_id', '=', $type . '_dictionary.id')
                    ->where($type . '_dictionary_trans.lang_id', '=', $language->id);
            })
            ->where('data_model_' . $type . '_fields.data_model_id', '=', $dm->id)
            ->where("form_groups.form_id", "=", $form->id)
            ->groupBy('field_trans_name',
                      $type . '_fields.id',
                      'field_types.name',
                'data_model_' . $type . '_fields.constraints',
                'form_groups.symbol_key',
                'form_groups.id',
                'field_order',
                'field_meta'
            )

            ->select(
                'field_types.name as field_type',
                $type . '_fields_trans.name as field_trans_name',
                $type . '_fields.name as field_name',
                $type . '_fields.default_value as field_default_value',

                DB::raw(Sql::getDbStatement()->groupConcat("{$type}_dictionary_trans.value", "field_choices", "{$type}_dictionary_trans.dictionary_id" )),
                DB::raw(Sql::getDbStatement()->groupConcat("{$type}_dictionary.value", "field_default_choices", "{$type}_dictionary.id")),
                DB::raw(Sql::getDbStatement()->groupConcat("{$type}_dictionary_trans.dictionary_id", "field_dictionary_id","{$type}_dictionary_trans.dictionary_id")),
                DB::raw(Sql::getDbStatement()->groupConcat("{$type}_dictionary.id", "field_default_dictionary_id","{$type}_dictionary.id")),
                $type.'_fields.symbol_key as field_symbol_key',
                'data_model_' . $type . '_fields.constraints as field_constraints',
                $type."_fields.id as field_id",
                'form_groups.symbol_key',
                'group_fields.order as field_order',
                DB::raw('(group_fields.meta)::text as field_meta'),
                'form_groups.id',
                'form_groups.name',
                'form_groups.depends_on',
                'form_groups.is_outer_dependent',
                'form_groups.order'
            );

        return
            new FieldDataTransformer($builder, new FieldToArrayTransformStrategy(['field_choices', 'field_default_choices', 'field_default_value', 'field_dictionary_id', 'field_default_dictionary_id']));

    }

} 