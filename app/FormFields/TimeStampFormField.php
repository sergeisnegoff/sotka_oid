<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class TimeStampFormField extends AbstractHandler
{
    protected $codename = 'datetime';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('formfields.time_stamp', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
    }
}
