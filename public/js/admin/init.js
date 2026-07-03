// ----- INITIALIZATION -----
document.addEventListener('DOMContentLoaded', () => {
    if (typeof authToken !== 'undefined' && authToken) {
        showApp();
    } else {
        showLogin();
    }
});
