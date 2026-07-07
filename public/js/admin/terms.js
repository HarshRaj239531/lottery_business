(function () {
    window.loadTermsData = async function() {
        try {
            const res = await fetch('/api/admin/terms-conditions', { headers: getHeaders() });
            const json = await res.json();
            if (json.status && json.data) {
                document.getElementById('terms-title').value = json.data.title || 'Terms & Conditions';
                document.getElementById('terms-content').value = json.data.content || '';
            }
        } catch (err) {
            console.error('Failed to load terms data:', err);
        }
    };

    window.submitTermsForm = async function(e) {
        if (e) e.preventDefault();
        
        const successDiv = document.getElementById('terms-success');
        const errorDiv = document.getElementById('terms-error');
        successDiv.style.display = 'none';
        errorDiv.style.display = 'none';

        const title = document.getElementById('terms-title').value;
        const content = document.getElementById('terms-content').value;

        try {
            const res = await fetch('/api/admin/terms-conditions', {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify({ title, content })
            });
            const json = await res.json();
            if (json.status) {
                successDiv.style.display = 'block';
                setTimeout(() => { successDiv.style.display = 'none'; }, 3000);
            } else {
                errorDiv.textContent = json.message || 'Failed to update Terms & Conditions';
                errorDiv.style.display = 'block';
            }
        } catch (err) {
            console.error('Failed to save terms data:', err);
            errorDiv.textContent = 'Connection error. Please try again.';
            errorDiv.style.display = 'block';
        }
    };
})();
