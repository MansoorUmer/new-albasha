@extends('layouts.admin')

@section('title', __('order.Orders_List'))
@section('content-header', __('order.Orders_List'))
@section('content-actions')
@if(auth()->user()->hasRole('admin', 'cashier'))
<a href="{{route('cart.index')}}" class="btn btn-primary" style="border-radius: 10px; padding: 8px 20px;">
    <i class="fas fa-cart-plus mr-1"></i> {{ __('cart.title') }}
</a>
@endif
@if(auth()->user()->hasRole('admin'))
<a href="{{route('orders.export-sold-items')}}" class="btn btn-info" style="border-radius: 10px; padding: 8px 20px; margin-left: 5px;" title="Export Sold Items">
    <i class="fas fa-file-csv mr-1"></i> Sold Items
</a>
<a href="{{route('orders.export-daily-orders')}}" class="btn btn-success" style="border-radius: 10px; padding: 8px 20px; margin-left: 5px;" title="Export Daily Orders">
    <i class="fas fa-file-pdf mr-1"></i> Daily Orders
</a>
@endif
@endsection
@section('content')
<div class="card" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-7"></div>
            <div class="col-md-5">
                <form action="{{route('orders.index')}}">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="date" name="start_date" class="form-control" value="{{request('start_date')}}" style="border-radius: 8px;" />
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}" style="border-radius: 8px;" />
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary" type="submit" style="border-radius: 8px;">{{ __('order.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
        <table class="table table-hover">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th>{{ __('order.ID') }}</th>
                    <th>Table / Type</th>
                    <th>{{ __('order.Total') }}</th>
                    <th>{{ __('order.Received_Amount') }}</th>
                    <th>Order Status</th>
                    <th>{{ __('order.Status') }}</th>
                    <th>Cook Status</th>
                    <th>{{ __('order.To_Pay') }}</th>
                    <th>{{ __('order.Created_At') }}</th>
                    <th>{{ __('order.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td><strong>#{{$order->id}}</strong></td>
                    <td>
                        @if(!is_null($order->table_no))
                            <span class="badge badge-secondary" style="font-size: 0.9em;">Table {{$order->table_no}}</span>
                        @else
                            N/A
                        @endif
                        <br><small class="text-muted">{{ $order->type ?? '' }}</small>
                    </td>
                    <td>{{ config('settings.currency_symbol') }} {{$order->formattedTotal()}}</td>
                    <td>{{ config('settings.currency_symbol') }} {{$order->formattedReceivedAmount()}}</td>
                    <td>
                        @if($order->status === 'open')
                            <span class="badge" style="background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; font-size: 0.85em; padding: 5px 12px; border-radius: 20px;">
                                <i class="fas fa-clock mr-1"></i> Open
                            </span>
                        @else
                            <span class="badge" style="background: linear-gradient(135deg, #28a745, #20c997); color: #fff; font-size: 0.85em; padding: 5px 12px; border-radius: 20px;">
                                <i class="fas fa-check mr-1"></i> Completed
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($order->receivedAmount() == 0)
                            <span class="badge badge-danger" style="border-radius: 20px; padding: 5px 10px;">{{ __('order.Not_Paid') }}</span>
                        @elseif($order->receivedAmount() < $order->total())
                            <span class="badge badge-warning" style="border-radius: 20px; padding: 5px 10px;">{{ __('order.Partial') }}</span>
                        @elseif($order->receivedAmount() == $order->total())
                            <span class="badge badge-success" style="border-radius: 20px; padding: 5px 10px;">{{ __('order.Paid') }}</span>
                        @elseif($order->receivedAmount() > $order->total())
                            <span class="badge badge-info" style="border-radius: 20px; padding: 5px 10px;">{{ __('order.Change') }}</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClass = '';
                            if ($order->cook_status === 'pending') {
                                $statusClass = 'status-pending';
                            } elseif ($order->cook_status === 'preparing') {
                                $statusClass = 'status-preparing';
                            } elseif ($order->cook_status === 'complete') {
                                $statusClass = 'status-complete';
                            }
                        @endphp
                        <select class="form-control form-control-sm cook-status-select {{ $statusClass }}" data-order-id="{{ $order->id }}" style="border-radius: 8px; font-size: 0.85em;">
                            <option value="pending" {{ $order->cook_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="preparing" {{ $order->cook_status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="complete" {{ $order->cook_status == 'complete' ? 'selected' : '' }}>Complete</option>
                        </select>
                    </td>
                    <td>{{config('settings.currency_symbol')}} {{number_format($order->total() - $order->receivedAmount(), 2)}}</td>
                    <td><small>{{$order->created_at->format('M d, Y H:i')}}</small></td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group" style="gap: 6px;">
                            {{-- View Invoice --}}
                            <button
                                class="btn btn-sm btn-secondary btnShowInvoice"
                                data-toggle="modal"
                                data-target="#modalInvoice"
                                data-order-id="{{ $order->id }}"
                                data-customer-name="{{ $order->type }}"
                                data-total="{{ $order->total() }}"
                                data-received="{{ $order->receivedAmount() }}"
                                data-instruction="{{ $order->instruction}}"
                                data-items="{{ json_encode($order->items) }}"
                                data-created-at="{{ $order->created_at }}"
                                data-payment="{{ isset($order->payments) && count($order->payments) > 0 ? $order->payments[0]->amount : 0 }}"
                                style="border-radius: 6px; margin-right: 6px;"
                                title="View Invoice">
                                <i class="fas fa-eye" style="font-size: 1.25em;"></i>
                            </button>

                            {{-- Print Order Receipt --}}
                            <button
                                class="btn btn-sm btn-dark btnPrintReceipt"
                                data-order-id="{{ $order->id }}"
                                data-customer-name="{{ $order->type }}"
                                data-total="{{ $order->total() }}"
                                data-received="{{ $order->receivedAmount() }}"
                                data-instruction="{{ $order->instruction }}"
                                data-table-no="{{ $order->table_no }}"
                                data-items="{{ json_encode($order->items) }}"
                                data-created-at="{{ $order->created_at }}"
                                style="border-radius: 6px; margin-right: 6px;"
                                title="Print Order"
                            >
                                <i class="fas fa-print" style="font-size: 1.25em;"></i>
                            </button>

                            {{-- Send to Kitchen (Kitchen Print) --}}
                            <button
                                class="btn btn-sm btn-warning btnSendToKitchen"
                                data-order-id="{{ $order->id }}"
                                data-customer-name="{{ $order->type }}"
                                data-table-no="{{ $order->table_no }}"
                                data-instruction="{{ $order->instruction }}"
                                data-items="{{ json_encode($order->items) }}"
                                data-created-at="{{ $order->created_at }}"
                                style="border-radius: 6px; margin-right: 6px; color: #000;"
                                title="Send to Kitchen">
                                <i class="fas fa-utensils" style="font-size: 1.25em;"></i>
                            </button>

                            @if(auth()->user()->hasRole('admin', 'cashier'))
                                {{-- Add Items (only for open orders) --}}
                                @if($order->status === 'open')
                                    <a href="{{ route('cart.index') }}?order_id={{ $order->id }}&table_no={{ $order->table_no }}"
                                       class="btn btn-sm btn-info"
                                       style="border-radius: 6px; margin-right: 6px;"
                                       title="Add More Items">
                                        <i class="fas fa-plus" style="font-size: 1.25em;"></i>
                                    </a>
                                @endif

                                {{-- Checkout (only for open orders) --}}
                                @if($order->status === 'open')
                                    <button class="btn btn-sm btn-success btnCheckout"
                                        data-order-id="{{ $order->id }}"
                                        data-total="{{ $order->total() }}"
                                        data-received="{{ $order->receivedAmount() }}"
                                        data-table-no="{{ $order->table_no }}"
                                        style="border-radius: 6px; margin-right: 6px;"
                                        title="Checkout & Print Bill">
                                        <i class="fas fa-cash-register" style="font-size: 1.25em;"></i>
                                    </button>
                                @endif

                                {{-- Partial Payment (for completed orders with balance) --}}
                                @if($order->total() > $order->receivedAmount() && $order->status === 'completed')
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#partialPaymentModal" data-orders-id="{{ $order->id }}" data-remaining-amount="{{ $order->total() - $order->receivedAmount() }}" style="border-radius: 6px; margin-right: 6px;" title="Pay Partial">
                                        <i class="fas fa-money-bill" style="font-size: 1.25em;"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <th></th>
                    <th></th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($receivedAmount, 2) }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        </div>
        {{ $orders->render() }}
    </div>
</div>
@endsection

@section('model')
{{-- Invoice Modal --}}
<div class="modal fade" id="modalInvoice" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="modalInvoiceLabel"><i class="fas fa-file-invoice mr-2"></i>New Albasha POS</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Dynamic content --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Partial Payment Modal --}}
<div class="modal fade" id="partialPaymentModal" tabindex="-1" role="dialog" aria-labelledby="partialPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header">
                <h5 class="modal-title" id="partialPaymentModalLabel">Pay Partial Amount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="partialPaymentForm" method="POST" action="{{ route('orders.partial-payment') }}">
                    @csrf
                    <input type="hidden" name="order_id" id="modalOrderId" value="">
                    <div class="form-group">
                        <label for="partialAmount">Enter Amount to Pay</label>
                        <input type="number" class="form-control" step="0.01" id="partialAmount" name="amount" style="border-radius: 8px;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Submit Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="printableReceipt" style="display: none;"></div>
@endsection

@section('js')
<script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // View Invoice Modal
    $(document).on('click', '.btnShowInvoice', function(event) {
        var button = $(this);
        var orderId = button.data('order-id');
        var customerName = button.data('customer-name');
        var totalAmount = button.data('total');
        var instruction = button.data('instruction');
        var receivedAmount = button.data('received');
        var payment = button.data('payment');
        var createdAt = button.data('created-at');
        var items = button.data('items');

        $('#modalInvoice').modal('show');

        var modalBody = $('#modalInvoice').find('.modal-body');

        var itemsHTML = '';
        if (items) {
            items.forEach(function(item, index) {
                itemsHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.product.name}</td>
                    <td>${item.description || 'N/A'}</td>
                    <td>${parseFloat(item.product.price).toFixed(2)}</td>
                    <td>${item.quantity}</td>
                    <td>${(parseFloat(item.product.price) * item.quantity).toFixed(2)}</td>
                </tr>
                `;
            });
        }

        modalBody.html(`
        <div class="card" style="border: none;">
            <div class="card-header" style="background: #f8f9fa; border-radius: 8px;">
                Invoice <strong>${createdAt.split('T')[0]}</strong>
                <span class="float-right"> <strong>Status:</strong> ${
                    receivedAmount == 0?
                        '<span class="badge badge-danger">{{ __('order.Not_Paid') }}</span>':
                    receivedAmount < totalAmount ?
                        '<span class="badge badge-warning">{{ __('order.Partial') }}</span>':
                    receivedAmount == totalAmount?
                        '<span class="badge badge-success">{{ __('order.Paid') }}</span>':
                    receivedAmount > totalAmount?
                        '<span class="badge badge-info">{{ __('order.Change') }}</span>':''
                }</span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h6 class="mb-3">Type: <strong>${customerName || 'N/A'}</strong></h6>
                    </div>
                </div>
                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Unit Cost</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHTML}
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right" colspan="5">Instructions:</th>
                                <th class="right"><strong>${instruction || 'N/A'}</strong></th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="5">Total</th>
                                <th class="right"><strong>{{config('settings.currency_symbol')}} ${parseFloat(totalAmount).toFixed(2)}</strong></th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="5">Paid</th>
                                <th class="right"><strong>{{config('settings.currency_symbol')}} ${parseFloat(receivedAmount).toFixed(2)}</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        `);
    });

    // Partial Payment Modal
    $(document).ready(function() {
        $('#partialPaymentModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var orderId = button.data('orders-id');
            var remainingAmount = button.data('remaining-amount');
            var modal = $(this);
            modal.find('#modalOrderId').val(orderId);
            modal.find('#partialAmount').val(remainingAmount);
            modal.find('#partialAmount').attr('max', remainingAmount);
        });
    });
</script>

{{-- Cook Status Update --}}
<script>
    $(document).on('change', '.cook-status-select', function() {
        var select = $(this);
        var orderId = select.data('order-id');
        var newStatus = select.val();

        select.prop('disabled', true);

        $.ajax({
            url: '{{ route("orders.update-cook-status") }}',
            method: 'POST',
            data: {
                order_id: orderId,
                cook_status: newStatus,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                select.prop('disabled', false);
                // Update styling
                select.removeClass('status-pending status-preparing status-complete');
                select.addClass('status-' + newStatus);
            },
            error: function(xhr) {
                select.prop('disabled', false);
                alert('Failed to update status. Please try again.');
            }
        });
    });
</script>

{{-- Checkout Button --}}
<script>
    $(document).on('click', '.btnCheckout', function() {
        var button = $(this);
        var orderId = button.data('order-id');
        var total = parseFloat(button.data('total'));
        var received = parseFloat(button.data('received'));
        var remaining = total - received;

        Swal.fire({
            title: 'Checkout Order #' + orderId,
            html: `
                <p style="font-size: 1.1em; margin-bottom: 15px;">
                    Total: <strong>{{ config('settings.currency_symbol') }} ${total.toFixed(2)}</strong><br>
                    Already Paid: <strong>{{ config('settings.currency_symbol') }} ${received.toFixed(2)}</strong><br>
                    <span style="color: #e74c3c; font-size: 1.2em;">Remaining: <strong>{{ config('settings.currency_symbol') }} ${remaining.toFixed(2)}</strong></span>
                </p>
                <label for="swal-received" style="font-weight: 600;">Received Amount:</label>
            `,
            input: 'number',
            inputValue: remaining.toFixed(2),
            inputAttributes: {
                min: 0,
                step: '0.01',
                id: 'swal-received'
            },
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-cash-register mr-1"></i> Complete & Print Bill',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745',
            showLoaderOnConfirm: true,
            preConfirm: (amount) => {
                if (!amount || parseFloat(amount) <= 0) {
                    Swal.showValidationMessage('Please enter a valid amount');
                    return;
                }
                return $.ajax({
                    url: `/admin/orders/${orderId}/checkout`,
                    method: 'POST',
                    data: {
                        amount: parseFloat(amount),
                        _token: '{{ csrf_token() }}'
                    }
                }).then(function(res) {
                    return { ...res, receivedAmount: parseFloat(amount) };
                }).catch(function(err) {
                    Swal.showValidationMessage(err.responseJSON?.message || 'An error occurred');
                });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    title: 'Order Completed!',
                    html: `
                        <p>Order #${orderId} has been checked out.</p>
                        <p>Received: <strong>{{ config('settings.currency_symbol') }} ${result.value.receivedAmount.toFixed(2)}</strong></p>
                        <p>Change: <strong>{{ config('settings.currency_symbol') }} ${(result.value.receivedAmount - remaining).toFixed(2)}</strong></p>
                    `,
                    icon: 'success',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-print"></i> Print Bill',
                    denyButtonText: '<i class="fas fa-utensils"></i> Send to Kitchen',
                    cancelButtonText: 'Close',
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#f39c12',
                }).then((printResult) => {
                    if (printResult.isConfirmed) {
                        var printWindow = window.open(`/admin/orders/${orderId}/receipt`, '_blank');
                        if (printWindow) printWindow.focus();
                    } else if (printResult.isDenied) {
                        var printWindow = window.open(`/admin/orders/${orderId}/kitchen-receipt`, '_blank');
                        if (printWindow) printWindow.focus();
                    }
                    // Reload the page to reflect changes
                    location.reload();
                });
            }
        });
    });
</script>

{{-- Print Receipt --}}
<script>
    $(document).on('click', '.btnPrintReceipt', function () {
        const button = $(this);
        var orderId = button.data('order-id');
        const tableNo = button.data('table-no');
        const customerName = button.data('customer-name');
        const total = button.data('total');
        const received = button.data('received');
        const instruction = button.data('instruction');
        const createdAt = button.data('created-at');
        const items = button.data('items');

        let itemsHTML = '';
        items.forEach((item, index) => {
            itemsHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.product.name}</td>
                <td>${parseFloat(item.product.price).toFixed(2)}</td>
                <td>${item.quantity}</td>
                <td>${(parseFloat(item.product.price) * item.quantity).toFixed(2)}</td>
            </tr>
            `;
        });

        const html = `
        <div style="font-family: monospace; padding: 20px;">
            <h3 style="text-align: center;">New Albasha POS</h3>
            <h5>Order No : ${orderId}</h5>
            <h5>Table No : ${tableNo || 'N/A'}</h5>
            <p>Date: ${createdAt}</p>
            <p>Type: ${customerName || 'N/A'}</p>
            <hr>
            <table width="100%" border="1" cellspacing="0" cellpadding="4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHTML}
                </tbody>
            </table>
            <hr>
            <p><strong>Instruction:</strong> ${instruction || 'N/A'}</p>
            <p><strong>Total:</strong> {{ config('settings.currency_symbol') }} ${total}</p>
            <p><strong>Paid:</strong> {{ config('settings.currency_symbol') }} ${received}</p>
            <p><strong>Change:</strong> {{ config('settings.currency_symbol') }} ${(received - total).toFixed(2)}</p>
        </div>
        `;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`<html><head><title>Receipt</title></head><body>${html}</body></html>`);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    });

    {{-- Send to Kitchen (Kitchen Print) --}}
    $(document).on('click', '.btnSendToKitchen', function () {
        const button = $(this);
        var orderId = button.data('order-id');
        const tableNo = button.data('table-no');
        const customerName = button.data('customer-name');
        const instruction = button.data('instruction');
        const createdAt = button.data('created-at');
        const items = button.data('items');

        let itemsHTML = '';
        items.forEach((item, index) => {
            itemsHTML += `
            <tr>
                <td style="font-size: 1.4em; padding: 10px 0;"><strong>${item.quantity}</strong></td>
                <td style="font-size: 1.4em; padding: 10px 0;"><strong>${item.product.name}</strong></td>
            </tr>
            `;
        });

        const html = `
        <div style="font-family: monospace; padding: 10px;">
            <h2 style="text-align: center; border-bottom: 2px dashed #000; padding-bottom: 5px;">KITCHEN ORDER</h2>
            <p><strong>Order ID: #${orderId}</strong></p>
            <p><strong>Table: ${tableNo || 'N/A'}</strong></p>
            <p>Date: ${createdAt}</p>
            <p>Type: ${customerName || 'N/A'}</p>
            <hr style="border: 0; border-top: 1px solid #000;">
            <table width="100%" cellspacing="0">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th align="left">QTY</th>
                        <th align="left">ITEM</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHTML}
                </tbody>
            </table>
            <hr style="border: 0; border-top: 1px solid #000;">
            ${instruction ? `
                <div style="border: 2px solid #000; padding: 10px; margin-top: 10px;">
                    <strong>KITCHEN NOTES:</strong><br>
                    <span style="font-size: 1.2em;">${instruction}</span>
                </div>
            ` : ''}
            <div style="text-align: center; margin-top: 20px; border-top: 1px dashed #000; padding-top: 10px;">
                *** End of Order ***
            </div>
        </div>
        `;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`<html><head><title>Kitchen Order</title></head><body>${html}</body></html>`);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    });
</script>
@endsection
