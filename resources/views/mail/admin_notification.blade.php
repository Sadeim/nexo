<!DOCTYPE html>
<html>
<head>
    <title>{{ $type === 'consultation' ? 'Consultation Request' : 'Contact Message' }}</title>
</head>
<body>
    <h2>New {{ $type === 'consultation' ? 'Consultation Request' : 'Contact Message' }} Received</h2>

    <p><strong>Name:</strong> {{ $data->name }}</p>
    <p><strong>Email:</strong> {{ $data->email }}</p>

    @if($type === 'consultation')
        <p><strong>Phone:</strong> {{ $data->phone }}</p>
        <p><strong>Service:</strong> {{ $data->service }}</p>
    @else
{{--        <p><strong>Subject:</strong> {{ $data->subject }}</p>--}}
    @endif

    <p><strong>Message:</strong></p>
    <p>{{ $data->message }}</p>

    <p>Please respond to this message as soon as possible.</p>
</body>
</html>
