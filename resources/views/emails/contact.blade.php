<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Contact Form Submission</title>
</head>
<body>
    <h2>New Contact Form Submission</h2>

    <p><strong>Name:</strong> {{ $contact->name }}</p>
    <p><strong>Email:</strong> {{ $contact->email }}</p>
    <p><strong>Phone:</strong> {{ $contact->mobile }}</p>
    <p><strong>Category:</strong> {{ $contact->category }}</p>

    <p><strong>Message:</strong></p>
    <p>{{ $contact->message }}</p>

    @if(!empty($contact->design))
        @php $designUrl = asset('storage/designs/' . $contact->design); @endphp
        <p><strong>Attached Design File:</strong>
            <a href="{{ $designUrl }}" target="_blank" rel="noopener">{{ $designUrl }}</a>
        </p>
    @endif
</body>
</html>