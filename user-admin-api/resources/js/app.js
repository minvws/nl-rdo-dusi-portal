import "./bootstrap";

import { onDomReady } from "@minvws/manon/utils";
import { initCreatedUserForm } from "./createdUserForm";

const initForms = () => {
    const loading = document.getElementById("loading");
    if (loading) {
        loading.classList.add("hidden");
    }

    if (document.getElementById("created-user-form")) {
        initCreatedUserForm();
    }
};

onDomReady(initForms);
