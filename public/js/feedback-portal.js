document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('userEmail');
    const historyBtn = document.getElementById('btnRequestHistory');

    if (!emailInput || !historyBtn) return;

    // Simple Regex for valid email format
    const isValidEmail = (email) => {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    };

    // Listen for typing in the email field
    emailInput.addEventListener('onkeyup', function(e) {
        if (isValidEmail(e.target.value)) {
            historyBtn.classList.remove('d-none'); // Show button
        } else {
            historyBtn.classList.add('d-none');    // Hide button
        }
    });

    // Handle button click via AJAX so the form doesn't submit
    historyBtn.addEventListener('click', function() {
        const email = emailInput.value;
        const originalHtml = historyBtn.innerHTML;
        
        // UI Loading State
        historyBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
        historyBtn.disabled = true;

        // Send background request
        fetch('{{ route("feedback.requestHistory") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            // UI Success State
            historyBtn.innerHTML = '<i class="fa fa-check"></i> Link Sent!';
            historyBtn.classList.replace('btn-outline-info', 'btn-success');
            
            // Reset button after 3 seconds
            setTimeout(() => {
                historyBtn.innerHTML = originalHtml;
                historyBtn.classList.replace('btn-success', 'btn-outline-info');
                historyBtn.disabled = false;
            }, 3000);
        })
        .catch(error => {
            console.error('Error:', error);
            historyBtn.innerHTML = '<i class="fa fa-exclamation-circle"></i> Error Sending';
            historyBtn.classList.replace('btn-outline-info', 'btn-danger');
            
            setTimeout(() => {
                historyBtn.innerHTML = originalHtml;
                historyBtn.classList.replace('btn-danger', 'btn-outline-info');
                historyBtn.disabled = false;
            }, 3000);
        });
    });

});

