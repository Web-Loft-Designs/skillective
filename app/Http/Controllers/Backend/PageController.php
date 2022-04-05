<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\AppBaseController;
use App\Models\Page;
use App\Models\PageMeta;
use File;
use URL;
use Image;

class PageController extends AppBaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request){
        $page = new Page();
        return view('backend.pages.list', [
            'pages' => $page->getList($request->input(), getRoutePerPage( $request )),
            'filterValues' => $request->except('l'),
        ]);
    }

    public function add(Request $request){
        $tree = Page::getTree();
        return view('backend.pages.add', [
            'tree'=>$tree,
            'page_title' => "Create Page",
        ]);
    }

    public function edit(Request $request, Page $page){
        $tree = Page::getTree();
        return view('backend.pages.edit', [
            'currentitem' => $page,
            'tree'=>$tree,
            'page_title' => "Edit Page",
        ]);
    }

    /**
     * @param Request $request
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Page $page ){
        $this->validate($request, [
            'name' => 'required|max:255|unique:pages,name,'.$page->id,
            'title' => 'required|max:255',
        ]);

        $input = [
            'name' => $request->name,
            'title' => $request->title,
            'parent_id' => $request->parent>0?$request->parent:null,
            'content' => $request->__get('content')
        ];
        $page->update($input);

        $allMeta = $page->getAllMeta();
        if (isset($request->page_meta) && is_array($request->page_meta)){
            foreach ($request->page_meta as $meta_name=>$meta_value){
				if (isset($_POST['page_meta'][$meta_name]) && $_POST['page_meta'][$meta_name]=='' && $meta_value===null)
					$meta_value = '';

                $meta_value = $this->getUpdatedMetaValue($request->page_meta, $page, $meta_name, $meta_value);

                if (strpos($meta_name, '_remove_')===0)
                    continue; // ignore remove file checkbox

                if (null !== $meta_value){
                    PageMeta::updateMetaValue($page, $meta_name, $meta_value);
                    unset($allMeta[$meta_name]);
                }
            }
            foreach ($allMeta as $meta_name=>$meta_value){
                if (strpos($meta_name, '_remove_')===0)
                    continue; // ignore remove file checkbox
                if (is_array($meta_value))
                    PageMeta::updateMetaValue($page, $meta_name, '');
            }
        }

        $request->session()->flash('alert-success', 'Item updated!');

        return redirect('backend/page/' . $page->id);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|max:255|unique:pages',
            'title' => 'required|max:255',
        ]);

        $new_item = Page::create([
            'name' => $request->name,
            'title' => $request->title,
            'parent_id' => $request->parent>0?$request->parent:null,
            'content' => $request->__get('content')
        ]);

        if ($new_item){
            $request->session()->flash('alert-success', 'Page created!');
            return redirect(route('backend.page.edit', $new_item->id));
        }else{
            return redirect(route('backend.page.create'))->withInput();
        }
    }

    /**
     * @param Request $request
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Page $page ){
        if ($page->id>12 && !in_array( $page->id, array_values(Page::getSpecialPages()) )){
            $page->delete($page->id);
            $request->session()->flash('alert-success', 'Page deleted!');
        }else{
            $request->session()->flash('alert-danger', 'Page can\'t be deleted!');
        }
        return redirect( route('backend.pages'));
    }

    private function getUpdatedMetaValue($request_page_meta, Page $page, $meta_name, $meta_value){
        $new_meta_value = $meta_value;

        if (is_array($new_meta_value)){
            $currentValue = $page->getMetaValue($meta_name);
            foreach ($new_meta_value as $index=>$v_meta_value_array) {
                // remove or upload image for array values
                foreach ($v_meta_value_array as $k_meta_name => $v_meta_value) {
                    if (strpos($k_meta_name, '_current_')===0
                        && !isset($v_meta_value_array[str_replace('_current', '', $k_meta_name)])
                        && !isset($v_meta_value_array['_remove' . str_replace('_current', '', $k_meta_name)])
                    ){
                        $new_meta_value[$index][str_replace('_current', '', $k_meta_name)] = $v_meta_value;
                    } elseif (strpos($k_meta_name, '_remove') === 0 || strpos($k_meta_name, '_image') === 0) {
                        $currentUploadedValue = isset($currentValue[$index][str_replace('_remove', '', $k_meta_name)])? $currentValue[$index][str_replace('_remove', '', $k_meta_name)] : '';
                        $pathToUploaded = public_path() . $currentUploadedValue;
                        $new_meta_value[$index][str_replace('_remove', '', $k_meta_name)] = $this->uploadImage($v_meta_value_array, $k_meta_name, $v_meta_value, $pathToUploaded);
                    }
                }
            }
        }else{
            if (strpos($meta_name, '_remove_')===0 || strpos($meta_name, '_image_')===0){
                $pathToUploaded = public_path() . $page->getMetaValue( str_replace('_remove', '', $meta_name) );
                $new_meta_value = $this->uploadImage($request_page_meta, $meta_name, $meta_value, $pathToUploaded);
                if ( strpos($meta_name, '_remove_')===0 && !isset($request_page_meta[str_replace('_remove', '', $meta_name)]) )
                    PageMeta::updateMetaValue($page, str_replace('_remove', '', $meta_name), $new_meta_value);
            }
        }

        $meta_value = $new_meta_value;
        return $meta_value;
    }

    private function uploadImage($request_page_meta, $meta_name, $meta_value, $pathToUploaded){
        $new_meta_value = '';
        // new image not uploading but want to remove the old one
        $removeOld = ( strpos($meta_name, '_remove')===0 && !isset($request_page_meta[str_replace('_remove', '', $meta_name)]) ); // && 1==$request_page_meta[$meta_name]
        if ($removeOld){
            $meta_name = str_replace('_remove', '', $meta_name);
            if (file_exists($pathToUploaded) && !is_dir($pathToUploaded))
                unlink($pathToUploaded);
            $new_meta_value = '';
        }

        if ( strpos($meta_name, '_image')===0
            && $meta_value instanceof UploadedFile
            && $meta_value->isFile()
            && (substr($meta_value->getMimeType(), 0, 5) == 'image' || ($meta_value->getMimeType()=='text/plain' && $meta_value->getClientOriginalExtension()=='svg'))
        ){ // handle uploaded images
            $removeOld = (isset($request_page_meta['_remove'.$meta_name]));
            if ($removeOld){
                if (file_exists($pathToUploaded) && !is_dir($pathToUploaded))
                    unlink($pathToUploaded);
            }

            // files uploading
            $destination = 'uploads';
            $baseName = str_replace('.'.$meta_value->getClientOriginalExtension(), '', $meta_value->getClientOriginalName());
            $newName  = $baseName . '.' . $meta_value->getClientOriginalExtension();
            $index = 1;
            while (File::exists($destination.'/'.$newName)){
                $newName  = $baseName . '-'.$index . '.' . $meta_value->getClientOriginalExtension();
                $index++;
            }

            $sizes = [
//                '_image_slide' => [320, 320],
//                '_image_benefit' => [72, 48]
            ];

            if (isset($sizes[$meta_name]) && substr($meta_value->getMimeType(), 0, 5) == 'image')
                Image::make($meta_value)->fit($sizes[$meta_name][0], $sizes[$meta_name][1])->save($destination.'/'.$newName);
            else{
                $path = $meta_value->move($destination, $newName);
            }

            $new_meta_value = '/' . $destination . '/' .  $newName;
        }
        return $new_meta_value;
    }
}
