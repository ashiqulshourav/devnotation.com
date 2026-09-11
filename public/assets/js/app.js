(() => {
    'use strict';

    const form = document.getElementById('contact-form');
    const message = document.getElementById('message');
    const count = document.getElementById('message-count');
    const submit = document.getElementById('submit-button');

    if (message && count) {
        const updateCount = () => {
            count.textContent = String(message.value.length);
        };

        message.addEventListener('input', updateCount);
        updateCount();
    }

    if (form && submit) {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                form.reportValidity();
                return;
            }

            submit.disabled = true;
            submit.querySelector('span:first-child').textContent = 'Sending…';
        });
    }
})();
