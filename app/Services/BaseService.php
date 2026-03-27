<?php
namespace App\Services;

use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BaseService
{
    use ResponseTrait;

    /**
     * Environment debug status
     * @var boolean
     */
    public $onDebug;

    public $search = null;

    protected $responseFormat;

    protected $user;

    public function __construct()
    {
        // $this->onDebug = config('app.debug');

        if (Auth::check()) {
            $this->user = Auth::user();
        }
    }
    function generateRandomString($length = 80)
    {
        $karakkter = '012345678dssd9abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $panjang_karakter = strlen($karakkter);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $karakkter[rand(0, $panjang_karakter - 1)];
        }
        return $str;
    }

    public function codeGenerator()
    {
        $codeLetter = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 3);
        $codeNumber = substr(str_shuffle("1234567890"), 0, 3);
        return $codeLetter.$codeNumber;
    }
}
