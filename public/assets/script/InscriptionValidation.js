document.addEventListener("DOMContentLoaded", () => {
    console.log("validation-ajax.js chargé !");
    const form = document.querySelector("#formInscription");
    if (!form) {
        console.log("Formulaire #formInscription non trouvé !");
        return;
    }
    console.log("Formulaire trouvé :", form);

    const statusBox = document.querySelector("#formStatus");

    const map = {
        nom: { input: "#nom", err: "#nomError" },
        password: { input: "#password", err: "#passwordError" },
        confirm: { input: "#confirm", err: "#confirmError" }
    };


    function setStatus(type, msg) {
        if (!statusBox) return;
        if (!msg) {
            statusBox.className = "alert d-none";
            statusBox.textContent = "";
            return;
        }
        statusBox.className = `alert alert-${type}`;
        statusBox.textContent = msg;
    }

    function clearFeedback() {
        Object.keys(map).forEach((k) => {
            const input = document.querySelector(map[k].input);
            const err = document.querySelector(map[k].err);
            input.classList.remove("is-invalid", "is-valid");
            if (err) err.textContent = "";
        });
        setStatus(null, "");
    }

    function applyServerResult(data) {
        Object.keys(map).forEach((k) => {
            const input = document.querySelector(map[k].input);
            const err = document.querySelector(map[k].err);
            const msg = data.erreurs && data.erreurs[k] ? data.erreurs[k] : "";

            if (msg) {
                input.classList.add("is-invalid");
                input.classList.remove("is-valid");
                if (err) err.textContent = msg;
            } else {
                input.classList.remove("is-invalid");
                input.classList.add("is-valid");
                if (err) err.textContent = "";
            }
        });

        if (data.erreurs && data.erreurs._global) {
            setStatus("warning", data.erreurs._global);
        }
    }

    async function callValidate() {
        const fd = new FormData(form);
        const res = await fetch("/api/validation/inscription", {
            method: "POST",
            body: fd,
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        if (!res.ok) throw new Error("Erreur serveur lors de la validation.");
        return res.json();
    }

    form.addEventListener("submit", async (e) => {
        console.log("Submit event déclenché !");
        e.preventDefault();
        clearFeedback();

        const password = document.querySelector("#password").value;
        const confirmPassword = document.querySelector("#confirm").value;

        if (password !== confirmPassword) {
            const confirmInput = document.querySelector("#confirm");
            const confirmErr = document.querySelector("#confirmError");
            confirmInput.classList.add("is-invalid");
            if (confirmErr)
                confirmErr.textContent = "Les mots de passe ne correspondent pas.";
            setStatus("danger", "Veuillez corriger les erreurs.");
            return;
        }

        try {
            const data = await callValidate();
            applyServerResult(data);

            if (data.ok) {
                setStatus("success", "Inscription en cours ...");

                const nomInput = document.querySelector("#nom");
                if (data.nom && nomInput) {
                    nomInput.value = data.nom;
                }
                form.submit();
            } else {
                setStatus("danger", "Veuillez corriger les erreurs.");
            }

        } catch (err) {
            setStatus("warning", err.message || "Une erreur est survenue.");
        }
    });
    Object.keys(map).forEach((k) => {
        document.querySelector(map[k].input).addEventListener("blur", async () => {
            try {
                const data = await callValidate();
                applyServerResult(data);
            } catch (_) {}
        });
    });
});
