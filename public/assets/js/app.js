document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("contact-form");

    const statusMessage = document.getElementById("status-message");
    const statusMessages = {
        sent: {
            type: "success",
            message: "Message sent successfully. We’ll get back to you soon."
        },
        error: {
            type: "error",
            message: "Something went wrong while sending your message. Please try again."
        },
        invalid: {
            type: "error",
            message: "Please check the form and try again."
        },
        busy: {
            type: "error",
            message: "Too many requests. Please wait a little and try again."
        },
        forbidden: {
            type: "error",
            message: "Your request could not be verified. Please refresh and try again."
        }
    };

    if (statusMessage) {
        const status = statusMessages[statusMessage.dataset.status];

        if (status) {
            statusMessage.textContent = status.message;
            statusMessage.classList.remove("hidden");

            if (status.type === "success") {
                statusMessage.classList.add(
                    "border-[#B8D5F3]",
                    "bg-[#EAF2FC]",
                    "text-[#14457F]"
                );
            } else {
                statusMessage.classList.add("border-red-200", "text-red-700");
            }
        }
    }

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

    function showStatus(statusKey) {
        if (!statusMessage) {
            return;
        }

        const status = statusMessages[statusKey];

        if (!status) {
            return;
        }

        statusMessage.textContent = status.message;
        statusMessage.classList.remove(
            "hidden",
            "border-[#B8D5F3]",
            "bg-[#EAF2FC]",
            "text-[#14457F]",
            "border-red-200",
            "text-red-700"
        );

        if (status.type === "success") {
            statusMessage.classList.add(
                "border-[#B8D5F3]",
                "bg-[#EAF2FC]",
                "text-[#14457F]"
            );
        } else {
            statusMessage.classList.add("border-red-200", "text-red-700");
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

    form.addEventListener("submit", async (event) => {
        event.preventDefault();

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

        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: new FormData(form),
                headers: {
                    Accept: "application/json"
                }
            });
            const result = await response.json();

            showStatus(result.status);

            if (result.status === "sent") {
                form.reset();
                updateMessageCount();
            }
        } catch {
            showStatus("error");
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.classList.remove("opacity-60", "cursor-not-allowed");
                submitButton.textContent = submitButton.dataset.originalText || "Send message";
            }
        }
    });
});