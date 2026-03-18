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
        <div class="table-responsive">
        <table class="table table-hover">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th>{{ __('product.ID') }}</th>
                    <th>{{ __('product.Name') }}</th>
                    <th>{{ __('product.Image') }}</th>
                    <th>{{ __('product.Barcode') }}</th>
                    <th>{{ __('product.Price') }}</th>
                    <th>{{ __('product.Quantity') }}</th>
                    <th>{{ __('product.Status') }}</th>
                    <th>{{ __('product.Created_At') }}</th>
                    <th>{{ __('product.Updated_At') }}</th>
                    @if(auth()->user()->isAdmin())
                    <th>{{ __('product.Actions') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{$product->id}}</td>
                    <td><strong>{{$product->name}}</strong></td>
                    <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt="" style="border-radius: 8px;"></td>
                    <td><code>{{$product->barcode}}</code></td>
                    <td>{{ config('settings.currency_symbol') }} {{number_format($product->price, 2)}}</td>
                    <td>
                        <span class="badge badge-{{ $product->quantity < 10 ? 'danger' : 'success' }}" style="border-radius: 20px; padding: 5px 10px; font-size: 0.85em;">
                            {{$product->quantity}}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}" style="border-radius: 20px; padding: 5px 10px;">
                            {{$product->status ? __('common.Active') : __('common.Inactive') }}
                        </span>
                    </td>
                    <td><small>{{$product->created_at->format('M d, Y')}}</small></td>
                    <td><small>{{$product->updated_at->format('M d, Y')}}</small></td>
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
        {{ $products->render() }}
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