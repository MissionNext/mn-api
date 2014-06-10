<?php


namespace MissionNext\Models\Field;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface  IField  {

  public function choices();

  public function dataModels();

    /**
     * @return BelongsToMany
     */
    public function languages();

} 