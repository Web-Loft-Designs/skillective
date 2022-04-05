<?php
$total_pages = $paginator->lastPage();
$page = $paginator->currentPage();
$per_page = $paginator->perPage();
$limits = [10, 20, 30, 50, 100];
$pageLimits = array_combine($limits, $limits);

$paginator->appends($sortVars);
?>
@if ($paginator->perPage()<$paginator->total())
<div class="row pagination-info">
    <div class="left d-flex align-items-center w-100 justify-content-end">
        <span class="pagination-info-number">{{ ($page*$per_page - $per_page + 1) }} – {{ ($page*$per_page>$paginator->total())?$paginator->total():$page*$per_page }} of {{ $paginator->total() }}</span>
        <div class="pagination-arrows">
            @if ($paginator->lastPage() > 1)
                <a href="{{ $paginator->url( ($page>1) ? $page-1:1 ) }}" class="icon icon-arrow-left {{ ($page == 1) ? ' disabled' : '' }}"></a>
                <a href="{{ $paginator->url( ($page<$paginator->lastPage()) ? $page+1:$paginator->lastPage() ) }}" class="icon icon-arrow-right {{ ($page == $paginator->lastPage()) ? ' disabled' : '' }}"></a>
            @endif
        </div>
    </div>
    {{--<div class="right">--}}
        {{--View--}}
        {{--{!! Form::select('page-limit', $pageLimits, $per_page, ['id'=>'facility-view', 'class'=>'page-limit','data-url'=>$paginator->url(1)]) !!}--}}
    {{--</div>--}}
</div>
@endif