@extends('layouts.admin')
@section('content-header', __('dashboard.title'))
@section('content')
<div class="container-fluid">
   <div class="row">
      <div class="col-lg-3 col-6">
         <div class="small-box bg-info">
            <div class="inner">
               <h3>{{$orders_count}}</h3>
               <p>{{ __('dashboard.Orders_Count') }}</p>
            </div>
            <div class="icon">
               <i class="fas fa-shopping-bag"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
      <div class="col-lg-3 col-6">
         <div class="small-box bg-success">
            <div class="inner">
               <h3>{{config('settings.currency_symbol')}} {{number_format($income, 2)}}</h3>
               <p>{{ __('dashboard.Income') }}</p>
            </div>
            <div class="icon">
               <i class="fas fa-chart-line"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
      <div class="col-lg-3 col-6">
         <div class="small-box bg-danger">
            <div class="inner">
               <h3>{{config('settings.currency_symbol')}} {{number_format($income_today, 2)}}</h3>
               <p>{{ __('dashboard.Income_Today') }}</p>
            </div>
            <div class="icon">
               <i class="fas fa-calendar-day"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
      <div class="col-lg-3 col-6">
         <div class="small-box bg-warning">
            <div class="inner">
               <h3>{{$customers_count}}</h3>
               <p>{{ __('dashboard.Customers_Count') }}</p>
            </div>
            <div class="icon">
               <i class="fas fa-users"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
   </div>
</div>
<div class="container-fluid">
   <div class="row">
      <div class="col-md-6 my-2">
         <div class="card" style="border-radius: 12px;">
            <div class="card-header" style="background: linear-gradient(135deg, #e74c3c, #fd79a8); color: #fff; border-radius: 12px 12px 0 0; font-weight: 600;">
               <i class="fas fa-exclamation-triangle mr-2"></i>Low Stock Products
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th>Name</th>
                        <th>Barcode</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($low_stock_products as $product)
                     <tr>
                        <td><strong>{{$product->name}}</strong></td>
                        <td><code>{{$product->barcode}}</code></td>
                        <td>{{ config('settings.currency_symbol') }} {{number_format($product->price, 2)}}</td>
                        <td><span class="badge badge-danger" style="border-radius: 20px; padding: 4px 10px;">{{$product->quantity}}</span></td>
                        <td>
                           <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}" style="border-radius: 20px; padding: 4px 10px;">{{$product->status ? __('common.Active') : __('common.Inactive') }}</span>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <div class="col-md-6 my-2">
         <div class="card" style="border-radius: 12px;">
            <div class="card-header" style="background: linear-gradient(135deg, #fdcb6e, #e17055); color: #fff; border-radius: 12px 12px 0 0; font-weight: 600;">
               <i class="fas fa-fire mr-2"></i>Hot Products (This Month)
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th>Name</th>
                        <th>Barcode</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($current_month_products as $product)
                     <tr>
                        <td><strong>{{$product->name}}</strong></td>
                        <td><code>{{$product->barcode}}</code></td>
                        <td>{{ config('settings.currency_symbol') }} {{number_format($product->price, 2)}}</td>
                        <td>{{$product->quantity}}</td>
                        <td>
                           <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}" style="border-radius: 20px; padding: 4px 10px;">{{$product->status ? __('common.Active') : __('common.Inactive') }}</span>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <div class="col-md-6 my-2">
         <div class="card" style="border-radius: 12px;">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border-radius: 12px 12px 0 0; font-weight: 600;">
               <i class="fas fa-chart-bar mr-2"></i>Hot Products (Past 6 Months)
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th>Name</th>
                        <th>Barcode</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($past_months_products as $product)
                     <tr>
                        <td><strong>{{$product->name}}</strong></td>
                        <td><code>{{$product->barcode}}</code></td>
                        <td>{{ config('settings.currency_symbol') }} {{number_format($product->price, 2)}}</td>
                        <td>{{$product->quantity}}</td>
                        <td>
                           <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}" style="border-radius: 20px; padding: 4px 10px;">{{$product->status ? __('common.Active') : __('common.Inactive') }}</span>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <div class="col-md-6 my-2">
         <div class="card" style="border-radius: 12px;">
            <div class="card-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: #fff; border-radius: 12px 12px 0 0; font-weight: 600;">
               <i class="fas fa-trophy mr-2"></i>Best Selling Products
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th>Name</th>
                        <th>Barcode</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($best_selling_products as $product)
                     <tr>
                        <td><strong>{{$product->name}}</strong></td>
                        <td><code>{{$product->barcode}}</code></td>
                        <td>{{ config('settings.currency_symbol') }} {{number_format($product->price, 2)}}</td>
                        <td>{{$product->quantity}}</td>
                        <td>
                           <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}" style="border-radius: 20px; padding: 4px 10px;">{{$product->status ? __('common.Active') : __('common.Inactive') }}</span>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
