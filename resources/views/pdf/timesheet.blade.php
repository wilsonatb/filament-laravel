<h1>TimeSheets</h1>
<table class="table-auto">
    <thead>
        <tr>
            <th>Calendario</th>
            <th>Tipo</th>
            <th>Entrada</th>
            <th>Salida</th>
        </tr>
    </thead>
    <tbody>
        @foreach($timesheets as $timesheet)
            <tr>
                <td>{{ $timesheet->calendar->name }}</td>
                <td>{{ $timesheet->type }}</td>
                <td>{{ $timesheet->day_in }}</td>
                <td>{{ $timesheet->day_out }}</td>
            </tr>
        @endforeach
    </tbody>
</table>