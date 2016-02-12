@extends('layouts.print')

@section('title', $title)

@section('content')

    @include('print.prescription.pharmacy_info')

        <table border="0" cellpadding="0" cellspacing="0" class="content-table">
            <tr>
                <td class="label">
                    Name:
                </td>
                <td class="value">
                    <% $prescription->patient->name %>
                </td>
            </tr>
            <tr>
                <td class="label">
                    Dr:
                </td>
                <td class="value">
                    <% $prescription->prescriber->getFullName() %>
                </td>
            </tr>
            <tr>
                <td class="label">
                    Rx#:
                </td>
                <td class="value">
                    <% $prescription->id %>
                </td>
            </tr>
            <tr>
                <td class="label">
                    Date:
                </td>
                <td class="value">
                    <% $date %>
                </td>
            </tr>
            <tr>
                <td class="label">
                    Time:
                </td>
                <td class="value">
                    <% $time %>
                </td>
            </tr>
            <tr>
                <td class="label">
                    GCT#:
                </td>
                <td class="value">
                    <% $pharmacy_gct %>
                </td>
            </tr>
        </table>

        <table border="0" cellpadding="0" cellspacing="0" class="prescription-items-table">
            @foreach ($prescription->prescription_items as $k=>$prescription_item)
                <tr>
                    <td class="number">
                        <% $k+1 %>
                    </td>
                    <td class="product">
                        <% $prescription_item->product->title %>
                    </td>
                    <td class="total">
                        <% $prescription_item->total %>
                    </td>
                </tr>
            @endforeach
        </table>

        <table border="0" cellpadding="0" cellspacing="0" class="prescription-total-table">
            <tr>
                <td class="label">
                    Total:
                </td>
                <td class="total">
                    <% $prescription->total %>
                </td>
            </tr>
            <tr>
                <td class="label">
                    Ins.Pays:
                </td>
                <td class="total">
                    <% $prescription->insurance %>
                </td>
            </tr>
            <tr>
                <td class="label">
                    Patient:
                </td>
                <td class="total">
                    <% $prescription->total - $prescription->insurance %>
                </td>
            </tr>
            <tr>
                <td class="label">
                    Discount:
                </td>
                <td class="total">
                    <% $prescription->discount_flat %>
                </td>
            </tr>
            <tr>
                <td class="label">
                    Net Due:
                </td>
                <td class="total">
                    <% $prescription->net_due %>
                </td>
            </tr>
        </table>

    @include('print.prescription.pharmacist_info')

    <script type="text/javascript">
        $(document).ready(function() {
            window.print();
        });
    </script>

@endsection
