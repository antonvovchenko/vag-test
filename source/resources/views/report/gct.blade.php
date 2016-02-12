@extends('layouts.report')

@section('title', $title)

@section('content')

    Period: <% $start %> - <% $end %>

    <form method="get" action="" class="no-print">
        <table border="0" cellpadding="0" cellspacing="0" class="action-buttons-table">
            <tr>
                <td>
                    <input type="submit" name="action" value="Print" />
                </td>
                <td>
                    <input type="submit" name="action" value="Export to Excel" />
                </td>
            </tr>
        </table>
        <input type="hidden" name="start" value="<% $start %>" />
        <input type="hidden" name="end" value="<% $end %>" />
    </form>

    @if ($action == 'Print')
        <script type="text/javascript">
            $(document).ready(function() {
                window.print();
            });
        </script>
    @endif

@endsection
