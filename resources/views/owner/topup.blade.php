<!DOCTYPE html>
<html>
<head>
    <title>Kontör Yükle</title>
</head>
<body>
<h2>Kontör Yükle</h2>
@if(session('success'))
    <p>{{ session('success') }}</p>
@endif
<form method="POST" action="{{ route('admin.topup') }}">
    @csrf
    <label>Yüklemek istediğiniz miktar:</label>
    <input type="number" name="amount" required>
    <button type="submit">Yükle</button>
</form>
</body>
</html>
