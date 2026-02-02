@foreach($records as $record)
    <tr id="data_{{ $record->id }}">
        <td>{{ \App\Models\Courier::where('id',$record->payable_id)->first()->name }}</td>
        <td>{{ \Carbon\Carbon::parse($record->payment_date)->format('d.m.Y') }}</td>
        <td>{{ number_format($record->amount, 2) }} ₺</td>
        <td>{{ $record->note ?? '-' }}</td>
        <td>{{ date('d-m-Y H:i', strtotime($record->created_at)) }}</td>
        <td>
            <div class="d-flex">
                <a onclick="DeleteFunction({{ $record->id }})"
                   class="btn btn-danger shadow btn-xs sharp">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </td>
    </tr>
@endforeach
