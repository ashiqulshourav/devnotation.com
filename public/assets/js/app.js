

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#contact-form");

    if (!form) {
        return;
    }

    const fields = {
        name: {
            element: form.querySelector('[name="name"]'),
            message: "Please enter your name."
        },
        email: {
            element: form.querySelector('[name="email"]'),
            message: "Please enter a valid email address."
        },
        message: {
            element: form.querySelector('[name="message"]'),
            message: "Please enter your message."
        }
    };

    function getErrorElement(field) {
        return form.querySelector(
            `.field-error[data-error-for="${field}"]`
        );
    }

    function showError(field, message) {
        if (!field) return;

        field.classList.remove("is-valid");
        field.classList.add("is-invalid");
        field.setAttribute("aria-invalid", "true");

        const error = getErrorElement(field.name);

        if (error) {
            error.textContent = message;
            error.classList.add("is-visible");
        }
    }

    function clearError(field) {
        if (!field) return;

        field.classList.remove("is-invalid");
        field.classList.add("is-valid");
        field.setAttribute("aria-invalid", "false");

        const error = getErrorElement(field.name);

        if (error) {
            error.textContent = "";
            error.classList.remove("is-visible");
        }
    }

    function validateField(field) {
        if (!field) {
            return true;
        }

        const value = field.value.trim();

        if (field.name === "name") {
            if (!value) {
                showError(field, "Please enter your name.");
                return false;
            }

            if (value.length < 2) {
                showError(field, "Name must be at least 2 characters.");
                return false;
            }
        }

        if (field.name === "email") {
            if (!value) {
                showError(field, "Please enter your email address.");
                return false;
            }

            const emailPattern =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailPattern.test(value)) {
                showError(field, "Please enter a valid email address.");
                return false;
            }
        }

        if (field.name === "message") {
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
        }

        clearError(field);
        return true;
    }

    Object.values(fields).forEach(({ element }) => {
        if (!element) return;

        element.addEventListener("blur", () => {
            validateField(element);
        });

        element.addEventListener("input", () => {
            if (element.classList.contains("is-invalid")) {
                validateField(element);
            }
        });
    });

    form.addEventListener("submit", (event) => {
        let valid = true;
        let firstInvalidField = null;

        Object.values(fields).forEach(({ element }) => {
            if (!element) return;

            const fieldValid = validateField(element);

            if (!fieldValid) {
                valid = false;

                if (!firstInvalidField) {
                    firstInvalidField = element;
                }
            }
        });

        if (!valid) {
            event.preventDefault();

            firstInvalidField?.focus({
                preventScroll: false
            });
        }
    });
});