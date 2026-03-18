@extends('layouts.admin')

@section('title', __('product.Product_List'))
@section('content-header', __('product.Product_List'))
@section('content-actions')
@if(auth()->user()->isAdmin())
<a href="{{route('products.create')}}" class="btn btn-primary" style="border-radius: 10px; padding: 8px 20px;">
    <i class="fas fa-plus mr-1"></i> {{ __('product.Create_Product') }}
</a>
@endif
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card product-list" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <form action="{{route('products.index')}}" method="GET" class="d-flex" style="gap: 10px;">
                    <select name="sort" class="form-control" style="border-radius: 8px;" onchange="this.form.submit()">
                        <option value="name" {{request('sort') === 'name' ? 'selected' : ''}}>Name</option>
                        <option value="price" {{request('sort') === 'price' ? 'selected' : ''}}>Price</option>
                        <option value="quantity" {{request('sort') === 'quantity' ? 'selected' : ''}}>Quantity</option>
                        <option value="created_at" {{request('sort', 'created_at') === 'created_at' ? 'selected' : ''}}>Date</option>
                    </select>
                    <select name="direction" class="form-control" style="border-radius: 8px;" onchange="this.form.submit()">
                        <option value="asc" {{request('direction') === 'asc' ? 'selected' : ''}}>Asc</option>
                        <option value="desc" {{request('direction', 'desc') === 'desc' ? 'selected' : ''}}>Desc</option>
                    </select>
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{request('search')}}">
                    @endif
                </form>
            </div>
            <div class="col-md-5"></div>
            <div class="col-md-4">
                <form action="{{route('products.index')}}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or barcode..." value="{{request('search')}}" style="border-radius: 8px 0 0 8px;">
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{request('sort')}}">
                        @endif
                        @if(request('direction'))
                            <input type="hidden" name="direction" value="{{request('direction')}}">
                        @endif
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" style="border-radius: 0 8px 8px 0;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
        <table class="table table-hover">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th>{{ __('product.ID') }}</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            {{ __('product.Name') }}
                            <i class="fas fa-sort{{ request('sort') === 'name' ? (request('direction') === 'asc' ? '-up' : '-down') : '' }} ml-1"></i>
                        </a>
                    </th>
                    <th>{{ __('product.Image') }}</th>
                    <th>{{ __('product.Barcode') }}</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            {{ __('product.Price') }}
                            <i class="fas fa-sort{{ request('sort') === 'price' ? (request('direction') === 'asc' ? '-up' : '-down') : '' }} ml-1"></i>
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'quantity', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            {{ __('product.Quantity') }}
                            <i class="fas fa-sort{{ request('sort') === 'quantity' ? (request('direction') === 'asc' ? '-up' : '-down') : '' }} ml-1"></i>
                        </a>
                    </th>
                    <th>{{ __('product.Status') }}</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            {{ __('product.Created_At') }}
                            <i class="fas fa-sort{{ request('sort', 'created_at') === 'created_at' ? (request('direction') === 'asc' ? '-up' : '-down') : '' }} ml-1"></i>
                        </a>
                    </th>
                    @if(auth()->user()->isAdmin())
                    <th>{{ __('product.Actions') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr style="{{ $product->parent_id ? 'background-color: #fcfcfc;' : '' }}">
                    <td>{{$product->id}}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($product->parent_id)
                                <i class="fas fa-level-up-alt fa-rotate-90 text-muted mr-2 ml-2"></i>
                            @endif
                            <div>
                                <strong>{{$product->name}}</strong>
                                @if($product->variant_name)
                                    <span class="badge badge-info ml-1" style="font-size: 0.75em; border-radius: 6px;">{{ $product->variant_name }}</span>
                                @endif
                                @if(!$product->parent_id && $product->variants->count() > 0)
                                    <span class="badge badge-secondary ml-1" style="font-size: 0.7em; border-radius: 6px;">{{ $product->variants->count() }} Variants</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><img class="product-img" src="{{ $product->getImageUrl() }}" alt="" style="border-radius: 8px; width: 50px; height: 50px; object-fit: cover;"></td>
                    <td><code>{{$product->barcode}}</code></td>
                    <td>{{ config('settings.currency_symbol') }} {{number_format($product->price, 2)}}</td>
                    <td>
                        <span class="badge badge-{{ $product->total_quantity < 10 ? 'danger' : 'success' }}" style="border-radius: 20px; padding: 5px 10px; font-size: 0.85em;">
                            {{$product->total_quantity}}
                        </span>
                        @if(!$product->parent_id && $product->variants->count() > 0)
                            <small class="text-muted d-block">Total stock</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}" style="border-radius: 20px; padding: 5px 10px;">
                            {{$product->status ? __('common.Active') : __('common.Inactive') }}
                        </span>
                    </td>
                    <td><small>{{$product->created_at->format('M d, Y')}}</small></td>
                    @if(auth()->user()->isAdmin())
                    <td>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary" style="border-radius: 6px;"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-sm btn-danger btn-delete" data-url="{{route('products.destroy', $product)}}" style="border-radius: 6px;"><i class="fas fa-trash"></i></button>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        {{ $products->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script type="module">
    $(document).ready(function() {
        $(document).on('click', '.btn-delete', function() {
            var $this = $(this);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: '{{ __('product.sure ') }}',
                text: '{{ __('product.really_delete ') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('product.yes_delete ') }}',
                cancelButtonText: '{{ __('product.No ') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.post($this.data('url'), {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        $this.closest('tr').fadeOut(500, function() {
                            $(this).remove();
                        });
                    });
                }
            });
        });
    });
</script>
@endsection