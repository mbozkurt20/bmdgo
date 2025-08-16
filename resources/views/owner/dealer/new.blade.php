<!DOCTYPE html>
<html>
<head>
    <title>Dealer Kaydı</title>
</head>
<body>
<h2>Dealer Kaydı</h2>
<form method="POST" action="{{ route('dealer.store') }}">
    @csrf
    <label>İsim:</label>
    <input type="text" name="name" required>
    <br>
    <label>Telefon:</label>
    <input type="text" name="phone" required>
    <br>
    <label>Şifre:</label>
    <input type="password" name="password" required>
    <br>
    <label>Şifre Tekrar:</label>
    <input type="password" name="password_confirmation" required>
    <br>
    <label>Latitude:</label>
    <input type="text" name="lat" required>
    <br>
    <label>Longitude:</label>
    <input type="text" name="long" required>
    <br>
    <label>Şehir:</label>
    <input type="text" name="city" required>
    <br>
    <label>İlçe:</label>
    <input type="text" name="district" required>
    <br>
    <button type="submit">Kaydet</button>
</form>
</body>
</html>
