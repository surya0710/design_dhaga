<h2>New Contact Form Message</h2>

<p><strong>Name:</strong> {{ $contact->name ?? $name }}</p>
<p><strong>Email:</strong> {{ $contact->email ?? $email }}</p>
<p><strong>Phone:</strong> {{ $contact->mobile ?? $phone }}</p>
<p><strong>Category:</strong> {{ ucfirst($contact->category ?? $category) }}</p>

@if(!empty($contact->instagram))
    <p><strong>Instagram:</strong>
        <a href="{{ $contact->instagram }}" target="_blank" rel="noopener">{{ $contact->instagram }}</a>
    </p>
@endif

<p><strong>Message:</strong></p>
<p>{{ $contact->message ?? $messageText }}</p>

@if(!empty($contact->design))
    @php $designUrl = asset('storage/designs/' . $contact->design); @endphp
    <p><strong>Attached Design File:</strong>
        <a href="{{ $designUrl }}" target="_blank" rel="noopener">{{ $designUrl }}</a>
    </p>
@endif
