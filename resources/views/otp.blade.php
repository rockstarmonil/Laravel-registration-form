<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .timer {
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Email Verification</h4>
                </div>
                <div class="card-body">
                    <p class="mb-2">An OTP has been sent to your email. Please enter it below:</p>

                    <div class="mb-3">
                        <span>OTP will expire in: <span id="countdown" class="timer">01:30</span></span>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('verify.otp') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Verify</button>
                    </form>

                    <form method="POST" action="{{ route('resend.otp') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100" id="resendBtn" disabled>Resend OTP</button>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Countdown timer in seconds
    let timeLeft = 90;
    const countdownElement = document.getElementById('countdown');
    const resendBtn = document.getElementById('resendBtn');

    const timerInterval = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            countdownElement.textContent = '00:00';
            resendBtn.disabled = false;  // Enable resend button
        } else {
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            countdownElement.textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            timeLeft--;
        }
    }, 1000);
</script>

</body>
</html>
