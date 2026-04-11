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
    <p><strong>Phone:</strong> {{ $contact->phone }}</p>
    <p><strong>Category:</strong> {{ $contact->category }}</p>

    @if(!empty($contact->instagram))
        <p><strong>Instagram:</strong> {{ $contact->instagram }}</p>
    @endif

    <p><strong>Message:</strong></p>
    <p>{{ $contact->message }}</p>

    @if(!empty($contact->design))
        <p><strong>Attached Design File:</strong> {{ $contact->design }}</p>
    @endif
</body>
</html>