<div class="table-responsive">
    <table class="table table-custom table-btn-drop">
        <thead>
        <tr>
            <th>
                Name
            </th>
            <th>
                Company/Location
            </th>
            <th>
                Created At
            </th>
            <th>
            </th>
        </tr>
        </thead>
        <tbody>
        @if (count($items) > 0)
            @foreach ($items as $item)
                <tr>
                    <td class="table-text white-nowrap">
                        <div>{{ $item->name }}</div>
                    </td>
                    <td class="table-text white-nowrap">
                        <div>{{ $item->company }}</div>
                    </td>
                    <td class="table-text white-nowrap">
                        <div>{{ $item->created_at->format('m/d/Y') }}</div>
                    </td>
                    <td class="action text-right">
                        <div class="dropdown">
                            <a href="{{ route('backend.testimonials.edit', $item->id) }}" class="btn btn-left">Edit</a>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <form action="{{ route('backend.testimonials.destroy', $item->id) }}" method="POST" class="delete-item-form delete-item-form-{{ $item->id }}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    @include('backend.includes.delete-button-with-confirmation', ['onConfirmSelector'=>'.delete-item-form-' . $item->id])
                                </form>
                            </div>

                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr><td colspan="4"><h5> No items found</h5></td></tr>
        @endif
        </tbody>
    </table>

</div>

@include('backend.pagination.default', ['paginator' => $items, 'sortVars'=>$filterValues])