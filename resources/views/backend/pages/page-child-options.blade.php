@foreach($children as $child)
    <option value="{{ $child->id }}" @if( (old('parent') && old('parent')==$child->id)  || (isset($currentitem) && $currentitem->parent_id==$child->id) ){{ ' selected ' }}@endif>
    {{ $margin }}{{ $child->title }}
        @if(count($child->children))
            @include('backend.pages.page-child-options',['children' => $child->children, 'margin'=>'-'.$margin])
        @endif
    </option>
@endforeach