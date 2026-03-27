<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Session;

class Alert extends Component
{
    public $session;
    // public $message;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {   
        $type='';
        $message='';
        if(Session::has('success')){
            $type='success';
            $message=Session::get('success');
        }else if(Session::has('error')){
            $type='error';
            $message=Session::get('error');
        }else if(Session::has('info')){
            $type='info';
            $message=Session::get('info');
        }
        session::forget('success');
        session::forget('error');
        session::forget('info');
        
        return view('components.alert-template',['type'=>$type,'message'=>$message]);
    }
}
