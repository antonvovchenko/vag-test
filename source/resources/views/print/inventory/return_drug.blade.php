@extends('layouts.print')

@section('title', $title)

@section('content')

    <table border="0" cellpadding="0" cellspacing="0" class="return_drug_info">
        <tr>
            <td>
                <% $pharmacy_name %>
            </td>
            <td>
                DATE: <% $date %>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                LIST OF RETURNED ITEMS
            </td>
        </tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" class="return-drug-items-table">

        <tr>
            <td class="header">
                Returned to:
            </td>
            <td class="header" colspan="3" style="text-align: center;">
                <% $inventory->supplier->name %>
            </td>
        </tr>

        <tr>
            <td>
                Invoice
            </td>
            <td style="width: 400px;">
                Description
            </td>
            <td style="width: 50px;">
                Qty
            </td>
            <td style="width: 75px;">
                Expiry Date
            </td>
        </tr>

        <tr>
            <td>
                <% $inventory->id %>
            </td>
            <td>
                <% $inventory->product->title %>
            </td>
            <td>
                <% $inventory->quantity_on*-1 %>
            </td>
            <td>
                <% strtotime($inventory->getBatchExpiryDate()) ? date('m/d/Y', strtotime($inventory->getBatchExpiryDate())) : '' %>
            </td>
        </tr>

    </table>

    <script type="text/javascript">
        $(document).ready(function() {
            window.print();
        });
    </script>

@endsection
