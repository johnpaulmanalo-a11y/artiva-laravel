<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #222; line-height: 1.5;">
    <h2>New message from the Artiva contact form</h2>

    <p><strong>Name:</strong> {{ $contactMessage->name }}</p>
    <p><strong>Email:</strong> {{ $contactMessage->email }}</p>
    <p><strong>Subject:</strong> {{ $contactMessage->subject }}</p>

    <hr>

    <p style="white-space: pre-line;">{{ $contactMessage->message }}</p>

    <hr>
    <p style="font-size: 12px; color: #888;">
        Hit "Reply" to respond directly to {{ $contactMessage->email }}.
    </p>
</body>
</html>
