@extends('layouts.admin')

@section('title', __('order.Orders_List'))
@section('content-header', __('order.Orders_List'))
@section('content-actions')
<a href="{{route('cart.index')}}" class="btn btn-primary">{{ __('cart.title') }}</a>
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-7"></div>
            <div class="col-md-5">
                <form action="{{route('orders.index')}}">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="date" name="start_date" class="form-control" value="{{request('start_date')}}" />
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}" />
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary" type="submit">{{ __('order.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('order.ID') }}</th>
                    <th>{{ __('order.Customer_Name') }}</th>
                    <th>{{ __('order.Total') }}</th>
                    <th>{{ __('order.Received_Amount') }}</th>
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
                    <td>{{$order->id}}</td>
                    <td>@if(!is_null($order->table_no)) {{$order->table_no}}@else N/A @endif</td>
                    <td>{{ config('settings.currency_symbol') }} {{$order->formattedTotal()}}</td>
                    <td>{{ config('settings.currency_symbol') }} {{$order->formattedReceivedAmount()}}</td>
                    <td>
                        @if($order->receivedAmount() == 0)
                            <span class="badge badge-danger">{{ __('order.Not_Paid') }}</span>
                        @elseif($order->receivedAmount() < $order->total())
                            <span class="badge badge-warning">{{ __('order.Partial') }}</span>
                        @elseif($order->receivedAmount() == $order->total())
                            <span class="badge badge-success">{{ __('order.Paid') }}</span>
                        @elseif($order->receivedAmount() > $order->total())
                            <span class="badge badge-info">{{ __('order.Change') }}</span>
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
                        <select class="form-control cook-status-select {{ $statusClass }}" data-order-id="{{ $order->id }}">
                            <option value="pending" {{ $order->cook_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="preparing" {{ $order->cook_status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="complete" {{ $order->cook_status == 'complete' ? 'selected' : '' }}>Complete</option>
                        </select>
                    </td>
                    <td>{{config('settings.currency_symbol')}} {{number_format($order->total() - $order->receivedAmount(), 2)}}</td>
                    <td>{{$order->created_at}}</td>
                    <td>
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
                            data-payment="{{ isset($order->payments) && count($order->payments) > 0 ? $order->payments[0]->amount : 0 }}">
                            <ion-icon size="samll" name="eye"></ion-icon>
                        </button>
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
                        >
                            <ion-icon size="small" name="print"></ion-icon>
                        </button>


                    @if($order->total() > $order->receivedAmount())
                            <!-- Button for Partial Payment -->
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#partialPaymentModal" data-orders-id="{{ $order->id }}" data-remaining-amount="{{ $order->total() - $order->receivedAmount() }}">
                                Pay Partial Amount
                            </button>
                            <!-- Partial Payment Modal -->
                            <div class="modal fade" id="partialPaymentModal" tabindex="-1" role="dialog" aria-labelledby="partialPaymentModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
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
                                                    <input type="number" class="form-control" step="0.01" id="partialAmount" name="amount" value="{{ $order->total() - $order->receivedAmount() }}">
                                                </div>
                                                <button type="submit" class="btn btn-primary">Submit Payment</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($receivedAmount, 2) }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        {{ $orders->render() }}
    </div>
</div>
@endsection
@section('model')
<!-- Modal -->
<div class="modal fade" id="modalInvoice" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInvoiceLabel">New Albasha POS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Placeholder for dynamic content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="printableReceipt" style="display: none;"></div>
@endsection

@section('js')
<script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Use event delegation to bind to the document for dynamically generated elements
    $(document).on('click', '.btnShowInvoice', function(event) {
        console.log("Modal show event triggered!");

        // Fetch data from the clicked button
        var button = $(this); // Button that triggered the modal
        var orderId = button.data('order-id');
        var customerName = button.data('customer-name');
        var totalAmount = button.data('total');
        var instruction = button.data('instruction');
        var receivedAmount = button.data('received');
        var payment = button.data('payment');
        var createdAt = button.data('created-at');
        var items = button.data('items'); // Ensure this is correctly passed as a JSON

        // Log the data to ensure it's being captured correctly
        console.log({
            orderId,
            customerName,
            totalAmount,
            receivedAmount,
            createdAt,
            items
        });

        // Open the modal
        $('#modalInvoice').modal('show');

        // Populate the modal body with dynamic data (you can extend this part)
        var modalBody = $('#modalInvoice').find('.modal-body');

        // Construct items HTML if items exist
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

        // Update the modal body content
        modalBody.html(`
    <div class="card">
        <div class="card-header">
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
                    <h6 class="mb-3">To: <strong>${customerName || 'N/A'}</strong></h6>
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
                        <th class="text-right" colspan="5">
                          Instructions:
                        </th>
                        <th class="right">
                          <strong>${instruction}</strong>
                        </th>
                      </tr>
                      <tr>
                        <th class="text-right" colspan="5">
                          Total
                        </th>
                        <th class="right">
                          <strong>{{config('settings.currency_symbol')}} ${totalAmount}</strong>
                        </th>
                      </tr>

                      <tr>
                        <th class="text-right" colspan="5">
                          Paid
                        </th>
                        <th class="right">
                          <strong>{{config('settings.currency_symbol')}} ${receivedAmount}</strong>
                        </th>
                      </tr>
                    </tfood>
                </table>
            </div>
        </div>
    </div>
  </div>
</div>
`);
    });
    $(document).ready(function() {
    // Event handler when the partial payment modal is triggered
    $('#partialPaymentModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        // Get the order ID from data-attributes
        var orderId = button.data('orders-id');
        var remainingAmount = button.data('remaining-amount');

        // Find modal and set the order ID in the hidden field
        var modal = $(this);
        modal.find('#modalOrderId').val(orderId);
        modal.find('#partialAmount').attr('max', remainingAmount); // Set max value for partial payment
    });
});

</script>
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
                console.log('Status updated successfully');
                select.prop('disabled', false);
            },
            error: function(xhr) {
                console.error('Error updating status:', xhr.responseText);
                select.prop('disabled', false);
                alert('Failed to update status. Please try again.');
            }
        });
    });
</script>
    <script>
        document.querySelectorAll('.cook-status-select').forEach(select => {
            select.addEventListener('change', function () {
                this.classList.remove('status-pending', 'status-preparing', 'status-complete');
                this.classList.add('status-' + this.value);
            });
        });
    </script>
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
            <h5>Table No : ${tableNo}</h5>
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
            <p><strong>Instruction:</strong> ${instruction}</p>
            <p><strong>Total:</strong> {{ config('settings.currency_symbol') }} ${total}</p>
            <p><strong>Paid:</strong> {{ config('settings.currency_symbol') }} ${received}</p>
            <p><strong>Change:</strong> {{ config('settings.currency_symbol') }} ${(received - total).toFixed(2)}</p>
        </div>
    `;

        const printSection = document.getElementById('printableReceipt');
        printSection.innerHTML = html;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`<html><head><title>Receipt</title></head><body>${html}</body></html>`);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    });

</script>

@endsection
