<?php
$from_value = '';
$to_value = '';

$filter_value = '';
if (isset($filterValues[$filter_attribute . '_from']) && isset($filterValues[$filter_attribute . '_to'])){
    if ($filterValues[$filter_attribute . '_from']==$filterValues[$filter_attribute . '_to'])
        $filter_value = $filterValues[$filter_attribute . '_to'];
    else
        $filter_value = ($filterValues[$filter_attribute . '_from'] . ' - ' . $filterValues[$filter_attribute . '_to']);
}
?>
<input type="text" class="form-control has-dd-calendar searchnow-in-list{{ (isset($asyncList) && $asyncList==1)?'-async':'' }}" name="{{ $filter_attribute }}" value="{{ $filter_value }}" placeholder="" autocomplete="off" />
@if( (isset($filterValues[$filter_attribute . '_from']) && $filterValues[$filter_attribute . '_from'] !== '') || (isset($filterValues[$filter_attribute . '_to']) && $filterValues[$filter_attribute . '_to'] !== '') )
<?php
if (isset($resetVars[$filter_attribute . '_from'])){
    $from_value = $resetVars[$filter_attribute . '_from'];
    unset($resetVars[$filter_attribute . '_from']);
}
if (isset($resetVars[$filter_attribute . '_to'])){
    $to_value = $resetVars[$filter_attribute . '_to'];
    unset($resetVars[$filter_attribute . '_to']);
}
if (isset($resetVars[$filter_attribute])){
    unset($resetVars[$filter_attribute]);
}
?>
<a href="{{ route($reset_route, $resetVars) }}" class="icon icon-clear"></a>
@endif

<div class="dropdown-container">
    <div class="dropdown-header">
        <div class="details">
            Filter by the last:
        </div>
        <ul class="date-selector">
            <li data-days="7">7 Days</li>
            <li data-days="30">30 Days</li>
            <li data-days="90">90 Days</li>
            <li class="selected">Custom</li>
        </ul>
    </div>
    <div class="dropdown-content">
        <div class="field left">
            <label class="stacked">From</label>
            <input name="{{ $filter_attribute }}_from" class="small datemask searchnow-in-list{{ (isset($asyncList) && $asyncList==1)?'-async':'' }}" type="text" value="{{ $from_value }}" placeholder="mm/dd/yyyy"/>
        </div>
        <div class="dash left"> </div>
        <div class="field right">
            <label class="stacked">To</label>
            <input name="{{ $filter_attribute }}_to" class="small datemask searchnow-in-list{{ (isset($asyncList) && $asyncList==1)?'-async':'' }}" type="text" value="{{ $to_value }}" placeholder="mm/dd/yyyy"/>
        </div>
    </div>
    <div class="dropdown-footer">
        <div role="dropdown-clear" class="link link-grey">Clear</div>
        <div role="dropdown-today" class="link">Today</div>
        <div role="dropdown-apply" class="link">Apply</div>
    </div>
</div>