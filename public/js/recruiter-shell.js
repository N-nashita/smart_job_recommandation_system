/* Shared recruiter shell: icon sprite, sidebar, toast, logout, company details.
   Each page sets <body class="app" data-page="applicants"> and has <aside class="side glass" id="side"></aside>. */
(() => {
    const SPRITE = `<svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs>
    <symbol id="i-home" viewBox="0 0 24 24"><path d="M3 11l9-7 9 7v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.6-3.5 3.2-5.5 6.5-5.5s5.9 2 6.5 5.5M16 4.8a3.5 3.5 0 0 1 0 6.4M18 14.8c2 .6 3.2 2.4 3.5 5.2"/></symbol>
    <symbol id="i-brief" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="3"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/></symbol>
    <symbol id="i-cal" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 11h18"/></symbol>
    <symbol id="i-building" viewBox="0 0 24 24"><path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M16 9h2a2 2 0 0 1 2 2v10M2 21h20M8 8h4M8 12h4M8 16h4"/></symbol>
    <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h3M16 8l4 4-4 4M20 12H9"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 3l8 3v6c0 4.5-3.4 7.7-8 9-4.6-1.3-8-4.5-8-9V6z"/><path d="M9 12l2 2 4-4"/></symbol>
    <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
    <symbol id="i-phone" viewBox="0 0 24 24"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></symbol>
    <symbol id="i-refresh" viewBox="0 0 24 24"><path d="M20 11a8 8 0 0 0-14-4M4 4v4h4M4 13a8 8 0 0 0 14 4M20 20v-4h-4"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol>
    <symbol id="i-x" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24"><path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></symbol>
    <symbol id="i-file" viewBox="0 0 24 24"><path d="M7 3h7l5 5v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zM14 3v5h5M9 13h6M9 17h6"/></symbol>
    <symbol id="i-arrow-r" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></symbol>
    </defs></svg>`;
    const NAV = [
        ["overview", "/recruiter/dashboard", "Overview", "i-home"],
        ["applicants", "/recruiter/applicants", "Applicants", "i-users", "24"],
        ["jobs", "/recruiter/jobs", "My jobs", "i-brief"],
        ["interviews", "/recruiter/interviews", "Interviews", "i-cal"],
        ["company", "/recruiter/company", "Company", "i-building"],
    ];
    const page = document.body.dataset.page;
    document.body.insertAdjacentHTML("afterbegin", SPRITE);
    document.body.insertAdjacentHTML(
        "beforeend",
        '<div class="toast" id="toast" role="status"></div>',
    );

    const side = document.getElementById("side");
    side.innerHTML =
        `<a href="/index.html" class="logo"><i></i> JobSphere</a>` +
        NAV.map(
            ([k, href, label, ic, n]) =>
                `<a class="nv ${k === page ? "on" : ""}" href="${href}"><svg class="ic"><use href="#${ic}"/></svg><span>${label}</span>${n ? `<em>${n}</em>` : ""}</a>`,
        ).join("") +
        `<a class="nv" href="#" id="logout"><svg class="ic"><use href="#i-logout"/></svg><span>Log out</span></a>
         <div class="grow"></div>
         <div class="co"><div class="row"><div class="avatar mint" id="coAv">A</div><div><strong id="coName">Acme Labs</strong><small id="coSub">Technology · Khulna</small></div></div>
         <span class="vb" id="coVb"><svg class="ic"><use href="#i-shield"/></svg><b>Verified company</b></span></div>`;

    window.toast = (msg) => {
        const t = document.getElementById("toast");
        t.textContent = msg;
        t.classList.add("show");
        clearTimeout(window.toast.h);
        window.toast.h = setTimeout(() => t.classList.remove("show"), 2400);
    };

    // real company details (falls back to the sample values if the backend is not there yet)
    fetch("/recruiter/dashboard-data", {
        headers: { Accept: "application/json" },
    })
        .then((r) => {
            if (r.status === 401) {
                location.href = "/login-recruiter.html";
                throw 0;
            }
            if (!r.ok) throw 0;
            return r.json();
        })
        .then((d) => {
            document.getElementById("coName").textContent = d.company_name;
            document.getElementById("coSub").textContent =
                d.industry + " · " + d.hq_location;
            document.getElementById("coAv").textContent = d.company_name[0];
            const vb = document.getElementById("coVb");
            vb.querySelector("b").textContent = {
                verified: "Verified company",
                pending: "Verification pending",
                rejected: "Verification failed",
            }[d.status];
            if (d.status !== "verified") {
                vb.style.background = "var(--blush)";
                vb.style.color = "#a65a40";
            }
            document.dispatchEvent(
                new CustomEvent("recruiter:ready", { detail: d }),
            );
        })
        .catch(() => {});

    document.getElementById("logout").addEventListener("click", async (e) => {
        e.preventDefault();
        try {
            await fetch("/recruiter/logout", {
                method: "POST",
                headers: { Accept: "application/json" },
            });
        } catch (_) {}
        location.href = "/login-recruiter.html";
    });
})();
