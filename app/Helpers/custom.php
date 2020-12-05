<?php 
define('LOGO_PATH','logo/');
define('USER_AVATAR_PATH','user/');
define('DATE_FORMAT',date('d M, Y',));
define('GENDER',['1'=>'Male','2'=>'Female']);
define('STATUS',['1'=>'Active','2'=>'Inactive']);
define('DELETABLE',['1'=>'No','2'=>'Yes']);
define('STATUS_LABEL',
['1'=>'<span class="badge badge-success">Active</span>',
'2'=>'<span class="badge badge-danger">Inactive</span>']);

define('MAIL_MAILER',['smtp','sendmal','mail']);
define('MAIL_ENCRYPTION',['none'=>'null','tls'=>'tls','ssl'=>'ssl']);

if(!function_exists('permission')){
    function permission(string $value){
        if(collect(\Illuminate\Support\Facades\Session::get('permission'))->contains($value)){
            return true;
        }
        return false;
    }
}

if(!function_exists('action_button')){
    function action_button($action){
        return '<div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-th-list text-white"></i>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        ' . $action . '
        </div>
      </div>';
    }
}
if(!function_exists('table_checkbox')){
    function table_checkbox($id){
        return '<div class="custom-control custom-checkbox">
            <input type="checkbox" value="'.$id.'"
            class="custom-control-input select_data" onchange="select_single_item('.$id.')" id="checkbox'.$id.'">
            <label class="custom-control-label" for="checkbox'.$id.'"></label>
        </div>';
    }
}
if(!function_exists('change_status')){
    function change_status(int $id,int $status,string $name = null){
        return $status == 1 ? '<span class="badge badge-success change_status" data-id="' . $id . '" data-name="' . $name . '" data-status="2" style="cursor:pointer;">Active</span>' : 
        '<span class="badge badge-danger change_status" data-id="' . $id . '" data-name="' . $name . '" data-status="1" style="cursor:pointer;">Inactive</span>';
    }
}