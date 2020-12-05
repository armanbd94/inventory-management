<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\MailSettingFormRequest;
use App\Http\Requests\GeneralSettingFormRequest;
use App\Models\Setting;
use App\Traits\UploadAble;
class SettingController extends BaseController
{
    use UploadAble;
    public function index(){
        if (permission('setting-access')) {
            $this->setPageData('Setting','Setting','fas fa-cogs');
            $zones_array = [];
            $timestamp = time();
            foreach (timezone_identifiers_list() as $key => $zone) {
                date_default_timezone_set($zone);
                $zones_array[$key]['zone'] = $zone;
                $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT '.date('P',$timestamp);
            }
            return view('setting.index',compact('zones_array'));
        } else {
            return $this->unauthorized_access_blocked();
        }
        
    }

    public function general_seting(GeneralSettingFormRequest $request){
        if($request->ajax())
        {
            try {
                $collection = collect($request->validated())->except(['logo','favicon']);
                foreach ($collection->all() as $key => $value) {
                    Setting::set($key,$value);
                    if ($key == 'timezone') {
                        if(!empty($value)){
                            $this->changeEnvData(['APP_TIMEZONE' => $value]);
                        }
                    }
                }

                if($request->hasFile('logo')){
                    $logo = $this->upload_file($request->file('logo'),LOGO_PATH);
                    if(!empty($request->old_logo)){
                        $this->delete_file($request->old_logo,LOGO_PATH);
                    }
                    Setting::set('logo',$logo);
                }
                if($request->hasFile('favicon')){
                    $favicon = $this->upload_file($request->file('favicon'),LOGO_PATH);
                    if(!empty($request->old_favicon)){
                        $this->delete_file($request->old_favicon,LOGO_PATH);
                    }
                    Setting::set('favicon',$favicon);
                }

                $output = ['status'=>'success','message'=>'Data Has Been Saved Successfully'];
                return response()->json($output);
            } catch (\Exception $e) {
                $output = ['status'=>'error','message'=> $e->getMessage()];
                return response()->json($output);
            }
            
        }

    }

    public function mail_setting(MailSettingFormRequest $request){
        if($request->ajax())
        {
            try {
                $collection = collect($request->validated());
                foreach ($collection->all() as $key => $value) {
                    Setting::set($key,$value);
                }

                $this->changeEnvData([
                    'MAIL_MAILER'     => $request->mail_mailer,
                    'MAIL_HOST'       => $request->mail_host,
                    'MAIL_PORT'       => $request->mail_port,
                    'MAIL_USERNAME'   => $request->mail_username,
                    'MAIL_PASSWORD'   => $request->mail_password,
                    'MAIL_ENCRYPTION' => $request->mail_encryption,
                    'MAIL_FROM_NAME'  => $request->mail_from_name
                ]);
                $output = ['status'=>'success','message'=>'Data Has Been Saved Successfully'];
                return response()->json($output);
            } catch (\Exception $e) {
                $output = ['status'=>'error','message'=> $e->getMessage()];
                return response()->json($output);
            }
            
        }
    }

    protected function changeEnvData(array $data)
    {
        if(count($data) > 0){
            $env = file_get_contents(base_path().'/.env');
            $env = preg_split('/\s+/',$env);

            foreach ($data as $key => $value) {
                foreach ($env as $env_key => $env_value) {
                    $entry = explode("=",$env_value,2);
                    if($entry[0] == $key){
                        $env[$env_key] = $key."=".$value;
                    }else{
                        $env[$env_key] = $env_value;
                    }
                }
            }
            $env = implode("\n",$env);

            file_put_contents(base_path().'/.env',$env);
            return true;
        }else {
            return false;
        }
    }
}
