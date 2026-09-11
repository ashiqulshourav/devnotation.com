document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("contact-form");

    if (!form) {
        return;
    }

    const fields = {
        email: form.querySelector('[name="email"]'),
        subject: form.querySelector('[name="subject"]'),
        message: form.querySelector('[name="message"]')
    };

    const messageCount = document.getElementById("message-count");

    function getErrorElement(field) {
        return form.querySelector(
            `[data-error-for="${field.name}"]`
        );
    }

    function showError(field, message) {
        if (!field) {
            return;
        }

        field.classList.remove("is-valid");
        field.classList.add("is-invalid");
        field.setAttribute("aria-invalid", "true");

        const error = getErrorElement(field);

        if (error) {
            error.textContent = message;
            error.classList.add("is-visible");
        }
    }

    function clearError(field) {
        if (!field) {
            return;
        }

        field.classList.remove("is-invalid");
        field.classList.add("is-valid");
        field.setAttribute("aria-invalid", "false");

        const error = getErrorElement(field);

        if (error) {
            error.textContent = "";
            error.classList.remove("is-visible");
        }
    }

    function validateEmail(field) {
        const value = field.value.trim();

        if (!value) {
            showError(field, "Please enter your email address.");
            return false;
        }

        if (value.length > 254) {
            showError(field, "Email address is too long.");
            return false;
        }

        const emailPattern =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailPattern.test(value)) {
            showError(field, "Please enter a valid email address.");
            return false;
        }

        clearError(field);
        return true;
    }

    function validateSubject(field) {
        const value = field.value.trim();

        if (!value) {
            showError(field, "Please enter a subject.");
            return false;
        }

        if (value.length > 150) {
            showError(
                field,
                "Subject must be 150 characters or less."
            );
            return false;
        }

        clearError(field);
        return true;
    }

    function validateMessage(field) {
        const value = field.value.trim();

        if (!value) {
            showError(field, "Please enter your message.");
            return false;
        }

        if (value.length < 10) {
            showError(
                field,
                "Message must be at least 10 characters."
            );
            return false;
        }

        if (value.length > 5000) {
            showError(
                field,
                "Message must be 5000 characters or less."
            );
            return false;
        }

        clearError(field);
        return true;
    }

    function validateField(field) {
        if (!field) {
            return true;
        }

        switch (field.name) {
            case "email":
                return validateEmail(field);

            case "subject":
                return validateSubject(field);

            case "message":
                return validateMessage(field);

            default:
                return true;
        }
    }

    function updateMessageCount() {
        if (messageCount && fields.message) {
            messageCount.textContent = String(fields.message.value.length);
        }
    }

    Object.values(fields).forEach((field) => {
        if (!field) {
            return;
        }

        field.addEventListener("blur", () => {
            validateField(field);
        });

        field.addEventListener("input", () => {
            if (field === fields.message) {
                updateMessageCount();
            }

            if (field.classList.contains("is-invalid")) {
                validateField(field);
            }
        });
    });

    updateMessageCount();

    form.addEventListener("submit", (event) => {
        let isValid = true;
        let firstInvalidField = null;

        Object.values(fields).forEach((field) => {
            if (!field) {
                return;
            }

            const fieldValid = validateField(field);

            if (!fieldValid) {
                isValid = false;

                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            }
        });

        if (!isValid) {
            event.preventDefault();

            firstInvalidField?.focus({
                preventScroll: false
            });

            return;
        }

        const submitButton = form.querySelector(
            'button[type="submit"], input[type="submit"]'
        );

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add("opacity-60", "cursor-not-allowed");

            const originalText =
                submitButton.textContent.trim();

            submitButton.dataset.originalText = originalText;

            if (submitButton.tagName === "BUTTON") {
                submitButton.textContent = "Sending...";
            }
        }
    });
});