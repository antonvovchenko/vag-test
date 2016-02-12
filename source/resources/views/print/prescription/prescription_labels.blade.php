@extends('layouts.print')

@section('title', $title)

@section('content')

    <script type="text/javascript">

        var documentTitle = document.title;

        function nl2br( str ) {
            return str.replace(/([^>])\n/g, '$1<br/>');
        }

        function printDoc(key){
            //hide from print another products
            $('.labels_box').addClass('no-print');
            $('.labels_box_'+key).removeClass('no-print');

            //show text and hide textareas
            $.each($('.textarea_labels'), function(k, v) {
                var item = v;
                $('.text_labels_'+$(item).attr('id')).html(nl2br( $(item).val())).show();
                $(item).hide();
                setTimeout(function(){
                    $('.text_labels_'+$(item).attr('id')).hide();
                    $(item).show();
                }, 1000);
            });

            //change title
            document.title = documentTitle+'-'+key;

            //print
            window.print();
        }
    </script>

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
    </table>

    <table border="0" cellpadding="0" cellspacing="0" class="prescription-labels-items-table">
        @foreach ($data as $k=>$item)
            <tr class="labels_box labels_box_<% $item['product']['key'] %>">
                <td class="product">
                    <% $item['product']['title'] %>
                </td>
                <td class="labels">
                    <div class="text_labels_<% $item['product']['key'] %>"></div>
                    <textarea class="no-print textarea_labels" id="<% $item['product']['key'] %>" style="resize: none;width: 100%;" rows="<% $item['rows']+2 %>"><% $item['labels'] %></textarea>
                </td>
                <td class="no-print">
                    <input type="button" onclick="printDoc(<% $item['product']['key'] %>)" name="Print" value="Print" />
                </td>
            </tr>
        @endforeach
    </table>

    @include('print.prescription.pharmacist_info')

@endsection
