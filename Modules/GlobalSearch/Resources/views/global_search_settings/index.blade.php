@extends('layouts.app')

@section('title', 'Global Search Settings')

@section('content')

<section class="content-header">
    <h1>Global Search Settings</h1>
</section>

<section class="content">
    <div class="box box-solid">
        <div class="box-body">

            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status.msg') }}
                </div>
            @endif

            <form action="{{ route('global_search_settings.store') }}" method="POST" id="global_search_settings_form">
                @csrf

                <div style="margin-bottom: 15px;">
                    <button type="button" class="btn btn-primary btn-sm" id="edit_btn">
                        <i class="fa fa-edit"></i> Edit
                    </button>

                    <button type="button" class="btn btn-success btn-sm" id="enable_all" disabled>
                        <i class="fa fa-check"></i> Enable All
                    </button>

                    <button type="button" class="btn btn-danger btn-sm" id="disable_all" disabled>
                        <i class="fa fa-times"></i> Disable All
                    </button>

                    <button type="button" class="btn btn-default btn-sm" id="cancel_btn" style="display:none;">
                        <i class="fa fa-times-circle"></i> Cancel
                    </button>
                </div>

                <div class="row">

                    <div class="col-md-6">

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_customer" value="1" {{ !empty($settings->allow_customer) ? 'checked' : '' }} disabled>
                                <strong>Customer</strong>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_supplier" value="1" {{ !empty($settings->allow_supplier) ? 'checked' : '' }} disabled>
                                <strong>Supplier</strong>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_invoice" value="1" {{ !empty($settings->allow_invoice) ? 'checked' : '' }} disabled>
                                <strong>Invoice</strong>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_purchase_order" value="1" {{ !empty($settings->allow_purchase_order) ? 'checked' : '' }} disabled>
                                <strong>Purchase Order</strong>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_vendor_bill" value="1" {{ !empty($settings->allow_vendor_bill) ? 'checked' : '' }} disabled>
                                <strong>Vendor Bill</strong>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_credit_memo" value="1" {{ !empty($settings->allow_credit_memo) ? 'checked' : '' }} disabled>
                                <strong>Credit Memo</strong>
                            </label>
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_vendor_credit_memo" value="1" {{ !empty($settings->allow_vendor_credit_memo) ? 'checked' : '' }} disabled>
                                <strong>Vendor Credit Memo</strong>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_expense" value="1" {{ !empty($settings->allow_expense) ? 'checked' : '' }} disabled>
                                <strong>Expense</strong>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_followup" value="1" {{ !empty($settings->allow_followup) ? 'checked' : '' }} disabled>
                                <strong>Followup</strong>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_product" value="1" {{ !empty($settings->allow_product) ? 'checked' : '' }} disabled>
                                <strong>Product</strong>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_menu" value="1" {{ !empty($settings->allow_menu) ? 'checked' : '' }} disabled>
                                <strong>Menu</strong>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="search-setting-checkbox" name="allow_report" value="1" {{ !empty($settings->allow_report) ? 'checked' : '' }} disabled>
                                <strong>Report</strong>
                            </label>
                        </div>

                    </div>

                </div>

                <hr>

                <button type="submit" class="btn btn-primary" id="save_btn" style="display:none;">
                    Save Settings
                </button>
            </form>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function () {
    var originalState = {};

    function captureOriginalState() {
        $('.search-setting-checkbox').each(function () {
            originalState[$(this).attr('name')] = $(this).prop('checked');
        });
    }

    function setEditMode(isEdit) {
        $('.search-setting-checkbox').prop('disabled', !isEdit);
        $('#enable_all').prop('disabled', !isEdit);
        $('#disable_all').prop('disabled', !isEdit);

        if (isEdit) {
            $('#save_btn').show();
            $('#cancel_btn').show();
            $('#edit_btn').hide();
        } else {
            $('#save_btn').hide();
            $('#cancel_btn').hide();
            $('#edit_btn').show();
        }
    }

    captureOriginalState();
    setEditMode(false);

    $('#edit_btn').on('click', function () {
        captureOriginalState();
        setEditMode(true);
    });

    $('#enable_all').on('click', function () {
        if ($(this).prop('disabled')) {
            return;
        }
        $('.search-setting-checkbox').prop('checked', true);
    });

    $('#disable_all').on('click', function () {
        if ($(this).prop('disabled')) {
            return;
        }
        $('.search-setting-checkbox').prop('checked', false);
    });

    $('#cancel_btn').on('click', function () {
        $('.search-setting-checkbox').each(function () {
            var name = $(this).attr('name');
            $(this).prop('checked', !!originalState[name]);
        });
        setEditMode(false);
    });
});
</script>
@endsection