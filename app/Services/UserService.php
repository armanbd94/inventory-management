<?php
namespace App\Services;

use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Repositories\UserRepository AS User;
use Carbon\Carbon;

class UserService extends BaseService{

    protected $user;

    public function __construct(User $user)
    {
        $this->user   = $user;
    }

    public function get_datatable_data(Request $request)
    {
        if ($request->ajax()) {

            if (!empty($request->name)) {
                $this->user->setName($request->name);
            }
            if (!empty($request->email)) {
                $this->user->setEmail($request->email);
            }
            if (!empty($request->role_id)) {
                $this->user->setRoleID($request->role_id);
            }
            if (!empty($request->mobile_no)) {
                $this->user->setMobileNo($request->mobile_no);
            }
            if (!empty($request->status)) {
                $this->user->setStatus($request->status);
            }

            $this->user->setOrderValue($request->input('order.0.column'));
            $this->user->setDirValue($request->input('order.0.dir'));
            $this->user->setLengthValue($request->input('length'));
            $this->user->setStartValue($request->input('start'));

            $list = $this->user->getDatatableList();

            $data = [];
            $no = $request->input('start');
            foreach ($list as $value) {
                $no++;
                $action = '';
                if(permission('user-edit')){
                $action .= ' <a class="dropdown-item edit_data" data-id="' . $value->id . '"><i class="fas fa-edit text-primary"></i> Edit</a>';
                }
                if(permission('user-view')){
                $action .= ' <a class="dropdown-item view_data"  data-id="' . $value->id . '"><i class="fas fa-eye text-warning"></i> View</a>';
                }
                if(permission('user-delete')){
                $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';
                }

                $row = [];
                if(permission('user-bulk-delete')){
                $row[] = table_checkbox($value->id);
                }
                $row[] = $no;
                $row[] = $this->avatar($value);
                $row[] = $value->name;
                $row[] = $value->role->role_name;
                $row[] = $value->email;
                $row[] = $value->mobile_no;
                $row[] = GENDER[$value->gender];
                $row[] = permission('user-edit') ? change_status($value->id,$value->status,$value->name) : STATUS_LABEL[$value->status];
                $row[] = action_button($action);
                $data[] = $row;
            }
            return $this->datatable_draw($request->input('draw'),$this->user->count_all(),
             $this->user->count_filtered(), $data);
        }
    }

    protected function avatar($user){
        if($user->avatar){
            return "<img src='storage/".USER_AVATAR_PATH.$user->avatar."' style='width:50px;'/>";
        }else{
            return "<img src='images/".($user->gender == 1 ? 'male' : 'female').".svg' style='width:50px;'/>";
        }
    }

    public function store_or_update_data(Request $request)
    {
        $collection = collect($request->validated())->except(['password','password_confirmation']);
        $created_at = $updated_at = Carbon::now();
        $created_by = $modified_by = auth()->user()->name;
        if($request->update_id){
            $collection = $collection->merge(compact('modified_by','updated_at'));
        }else{
            $collection = $collection->merge(compact('created_by','created_at'));
        }
        if($request->password){
            $collection = $collection->merge(with(['password' => $request->password ]));
        }
        return $this->user->updateOrCreate(['id'=>$request->update_id],$collection->all());
        
    }

    public function edit(Request $request)
    {
        return $this->user->find($request->id);
    }

    public function delete(Request $request)
    {
        return $this->user->delete($request->id);
    }

    public function bulk_delete(Request $request)
    {
        return $this->user->destroy($request->ids);
    }

    public function change_status(Request $request)
    {
        return $this->user->find($request->id)->update(['status'=>$request->status]);
    }


    
}