<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Http\Request;

class SetFormRequest extends Component
{
    private $item;
    // public $message;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {   
        $this->item=!empty($request->get('gsf')) ? $request->get('gsf') : '';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {   
        return view('components.set-form-request-template',['item'=>$this->item]);
    }
}
