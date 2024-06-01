<?php
namespace Dalailama\Validation;

use Dalailama\Http\Request;
use Dalailama\Http\Url;
use Dalailama\Session\Session;
use Rakit\Validation\Validator;
use Dalailama\Validation\Rules\UniqueRule;
class Validate
{
    private function __construct(){}

    public static function validate($rules, $json = null)
    {
        $validator = new Validator;
        $validator->addValidator('unique', new UniqueRule());

        $validation = $validator->validate($_POST + $_FILES, $rules);

        $errors = $validation->errors();

        if ($validation->fails()) {
            if ($json) {
                return ['errors' => $errors->firstOfAll()];
            } else {
                Session::set('errors', $errors);
                Session::set('old', Request::all());

                return Url::redirect(Url::previous());
            }
        }
    }
}