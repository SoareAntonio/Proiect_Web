function logoutUser() {
    localStorage.removeItem('token_zoo');
    localStorage.removeItem('role_zoo');
    window.location.href = 'login.html';
}

function updateNavbarAuth() {
    const token = localStorage.getItem('token_zoo');
    const role = localStorage.getItem('role_zoo');

    const profileLink = document.querySelector('nav .user-profile');
    const navActions = document.querySelector('nav .nav-actions');

    if (!profileLink || !navActions) return;

    const avatar = profileLink.querySelector('.avatar');
    const userName = profileLink.querySelector('.user-name');

    if (!token) {
        profileLink.href = 'login.html';

        if (avatar) avatar.textContent = 'AD';
        if (userName) userName.textContent = 'Login';

        const existingLogout = document.getElementById('logout-link');
        if (existingLogout) existingLogout.remove();

        return;
    }

    profileLink.href = 'profile.html';

    if (avatar) {
        avatar.textContent = role === 'admin' ? 'AD' : 'US';
    }

    if (userName) {
        userName.textContent = 'Profil';
    }

    let logoutLink = document.getElementById('logout-link');

    if (!logoutLink) {
        logoutLink = document.createElement('a');
        logoutLink.id = 'logout-link';
        logoutLink.className = 'btn-exit';
        logoutLink.href = '#';
        logoutLink.textContent = 'Logout';

        navActions.appendChild(logoutLink);
    }

    logoutLink.addEventListener('click', function (event) {
        event.preventDefault();
        logoutUser();
    });
}

document.addEventListener('DOMContentLoaded', updateNavbarAuth);