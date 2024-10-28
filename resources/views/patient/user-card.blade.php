<!-- resources/views/user-card.blade.php -->
<div style="width: 300px; border: 2px solid #4A5568; padding: 20px; text-align: center; font-family: Arial, sans-serif; border-radius: 8px;">
  <!-- Profile Image -->
  <div style="margin-bottom: 15px;">
      <img src="{{ $user->getAvarUrl(true) }}" style="width: 1in; height: 1in; object-fit: cover; border-radius: 50%; border: 2px solid #4A5568;">
  </div>

  <!-- User Information -->
  <h2 style="font-size: 18px; margin: 0; color: #2D3748;">{{ $user->name }}</h2>
  <p style="font-size: 12px; color: #718096; margin: 5px 0;">Email: {{ $user->email }}</p>
  <p style="font-size: 12px; color: #718096; margin: 5px 0;">Phone: {{ $user->phone }}</p>

  <!-- Barcode -->
  <div style="margin-top: 20px;">
      <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($user->id, 'C39') }}" alt="barcode" style="width: 80%; height: 50px;">
  </div>
</div>