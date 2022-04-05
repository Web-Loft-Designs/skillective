<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\AppBaseController;
use App\Models\Setting;
use App\Http\Requests\UpdateSettingsRequest;
use App;
use File;
use URL;
use Image;

class SettingsController extends AppBaseController
{

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request){
        $settings = app::make('App\Models\Setting')->getAllAssociative();
        return view('backend.settings.list', [
            'settings' => $settings,
            'page_title' => 'Settings'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(UpdateSettingsRequest $request){
        if (isset($request->settings) && is_array($request->settings)){
            $settingsModel = app::make('App\Models\Setting');
            $saved_settings = $settingsModel->getAllAssociative();
			$settingsModel->removeAllFromCache();

            $files = ['terms_and_conditions_file'];
            $images = ['sitelogo'=>'300x100', 'sitelogo-mobile'=>'200x100', 'default_profile_image' => '200x200', 'favicon'=>'24x24'];
            $destination = 'uploads';

            // remove files marked to delete
            foreach (array_merge($files, array_keys($images)) as $settings_name){
                $settings_value = null;
                $removeOld = (isset($request->settings['_remove_'.$settings_name]) && 1==$request->settings['_remove_'.$settings_name]);
                if ($removeOld){
                    $base_url = trim(URL::to('/'), '/' . $destination);
                    File::delete(str_replace($base_url . '/', '', $saved_settings[$settings_name]));
                    $settings_value = '';
                    $settingsModel->updateValue($settings_name, $settings_value);
                }
            }

            foreach ($request->settings as $settings_name=>$settings_value){
                // uploading Files
                if ( in_array($settings_name, $files)){ // handle uploaded images
                    if ($settings_value instanceof UploadedFile && $settings_value->isFile()){
                        // files uploading
                        $baseName = str_replace('.'.$settings_value->getClientOriginalExtension(), '', $settings_value->getClientOriginalName());
                        $newName  = $baseName . '.' . $settings_value->getClientOriginalExtension();
                        $index = 1;
                        while (File::exists($destination.'/'.$newName)){
                            $newName  = $baseName . '-'.$index . '.' . $settings_value->getClientOriginalExtension();
                            $index++;
                        }
                        $path = $settings_value->move($destination, $newName);
                        $settings_value = '/' . $destination . '/' . $newName;//url(str_replace('\\', '/', $path));
                    }
                }
				
                // uploading Images
                if ( isset($images[$settings_name])){ // handle uploaded images
                    if ($settings_value instanceof UploadedFile && $settings_value->isFile()){
                        $size = explode('x', $images[$settings_name]);
                        // files uploading
                        $baseName = str_replace('.'.$settings_value->getClientOriginalExtension(), '', $settings_value->getClientOriginalName());
                        $newName  = $baseName . '.' . $settings_value->getClientOriginalExtension();
                        $index = 1;
                        while (File::exists($destination.'/'.$newName)){
                            $newName  = $baseName . '-'.$index . '.' . $settings_value->getClientOriginalExtension();
                            $index++;
                        }
                        $img = Image::make($settings_value);
                        $img->fit($size[0], $size[1]);
                        $img->save( $destination . '/' . $newName ); // $path = $settings_value->move($destination, $newName);
                        $settings_value = '/' . $destination . '/' . $newName;
                    }
                }

                if (strpos($settings_name, '_remove_')===0) continue; // remove file checkbox
                if (null === $settings_value)
                    $settings_value = '';

                // menus prepare to save
                if (is_array($settings_value)){
                    if ($request->has($settings_name.'_order')){ // + ordering
                        parse_str($request->input($settings_name.'_order'), $currentSectionFieldsOrder);
                        $currentSectionFieldsOrder = array_keys(array_shift($currentSectionFieldsOrder));
                        $value = [];
                        foreach ($settings_value as $name=>$values_array){
                            foreach ($currentSectionFieldsOrder as $k=>$index){
                                if (isset($settings_value[$name][$index])){
                                    if (!isset($value[$index]))
                                        $value[$index] = [];
                                    $value[$index][$name] = $settings_value[$name][$index];
                                }
                            }
                        }
                        $settings_value = serialize($value);
                    }else // save as is
                        $settings_value = serialize($settings_value);
                }

                $settingsModel->updateValue($settings_name, $settings_value);
            }
			$settingsModel->putAllToCache();
        }

        $request->session()->flash('alert-success', 'Settings updated!');

        return redirect( route('backend.settings') );
    }
}
