<?php
/**
 * Created by PhpStorm.
 * User: nikolai
 * Date: 14.04.14
 * Time: 11:21
 */

namespace App\Models\Job;


use App\Models\Field\IField;

interface IJobField extends IField
{
   public function jobs();
}
