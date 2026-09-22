/**
 * Staff PWA helpers: geolocation clocking + service worker registration.
 */
async function getPosition() {
    if (!navigator.geolocation) {
        throw new Error('Location is not supported on this device.');
    }

    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, {
            enableHighAccuracy: true,
            timeout: 20000,
            maximumAge: 0,
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-clock-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = form.querySelector('[type="submit"]');
            const status = form.querySelector('[data-clock-status]');
            const latInput = form.querySelector('[name="lat"]');
            const lngInput = form.querySelector('[name="lng"]');
            const accuracyInput = form.querySelector('[name="accuracy"]');

            if (button) {
                button.disabled = true;
            }
            if (status) {
                status.textContent = 'Getting GPS…';
            }

            try {
                const position = await getPosition();
                latInput.value = position.coords.latitude;
                lngInput.value = position.coords.longitude;
                if (accuracyInput) {
                    accuracyInput.value = position.coords.accuracy ?? '';
                }
                if (status) {
                    status.textContent = 'Submitting…';
                }
                form.submit();
            } catch (error) {
                if (status) {
                    status.textContent = '';
                }
                if (button) {
                    button.disabled = false;
                }
                alert(error?.message || 'Could not read your location. Enable GPS and try again.');
            }
        });
    });

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/staff-sw.js').catch(() => {});
    }
});
