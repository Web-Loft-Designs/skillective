<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'system_settings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'value'
    ];

    /**
     * @return array
     */
    public function getAllAssociative(){
        $settings = [];
        $all = $this->all();
        foreach ($all as $s){
            $settings[$s->name] = (preg_match('/-menu$/', $s->name))?unserialize($s->value):$s->value;
        }
        return $settings;
    }

    /**
     * @param $settings_name
     * @param $settings_value
     * @return void
     */
    public function updateValue($settings_name, $settings_value){
        $all = $this->all();
        $setting = null;
        foreach ($all as $s){
            if ($settings_name==$s->name){
                $setting = $s;
            }
        }

        $attributes = ['value'=>is_array($settings_value)?serialize($settings_value):$settings_value];
        if ($setting && $setting->id){
            $setting->update($attributes);
        }else{
            $attributes['name'] = $settings_name;
            $this->create($attributes);
        }
    }

    /**
     * @param $settings_name
     * @return void
     */
    public function incrementValue($settings_name){
		$setting = self::where('name', $settings_name)->first();
		if ($setting && is_numeric($setting->value)){
			$newValue = $setting->value + 1;
			$setting->update(['value' => $newValue]);
		}else{
			$this->create(['name'=>$settings_name, 'value'=>1]);
		}
	}

    /**
     * @param $settings_name
     * @param $defaultValue
     * @param $fromCache
     * @return mixed|string
     */
    public static function getValue($settings_name, $defaultValue = '', $fromCache = true){
    	if ( $fromCache && ($val = Cache::get("settings.{$settings_name}"))!=null ){
    		return $val;
		}else{
			$row = self::where('name', $settings_name)->first();
			if ($row){
				Cache::forever("settings.{$row->name}", $row->value);
				return $row->value;
			}
		}
        return $defaultValue;
    }

    /**
     * @return void
     */
    public function putAllToCache(){
    	$all = $this->getAllAssociative();
    	foreach ($all as $key=>$value){
			Cache::forever("settings.{$key}", $value);
		}
	}

    /**
     * @return void
     */
    public function removeAllFromCache(){
		$all = $this->getAllAssociative();
		foreach ($all as $key=>$value){
			Cache::forget("settings.{$key}");
		}
	}
}
