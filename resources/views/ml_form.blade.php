<!DOCTYPE html>
<html>
<head>
    <title>ML Prediction</title>
</head>
<body>
    <h1>Send Text to ML Service</h1>

    <form action="{{ route('ml.call') }}" method="POST">
        @csrf
        <label for="text">Enter text:</label>
        <input type="text" name="text" id="text" required>
        <button type="submit">Predict</button>
    </form>

    @isset($result)
        <h2>Prediction Result:</h2>
        <p>{{ $result }}</p>
    @endisset
</body>
</html>
