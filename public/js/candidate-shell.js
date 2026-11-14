/* Candidate pages: toast, logout, apply/save fetch helper, scroll-reveal. */
(() => {
    const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

    window.toast = (msg) => {
        const t = document.getElementById("toast");
        t.textContent = msg;
        t.classList.add("show");
        clearTimeout(window.toast.h);
        window.toast.h = setTimeout(() => t.classList.remove("show"), 2400);
    };

    const logoutBtn = document.getElementById("cand-logout");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", async (e) => {
            e.preventDefault();
            try {
                await fetch("/logout", {
                    method: "POST",
                    headers: { Accept: "application/json", "X-CSRF-TOKEN": csrf() },
                });
            } catch (_) {}
            location.href = "/candidate/login";
        });
    }

    // JSON fetch helper (CSRF + error body -> Error with .data)
    window.api = async (url, method, body) => {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const res = await fetch(url, {
            method,
            headers: {
                "X-CSRF-TOKEN": token,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            body: body ? JSON.stringify(body) : undefined,
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            throw Object.assign(
                new Error(data.message || "Something went wrong. Please try again."),
                { data }
            );
        }
        return data;
    };

    const els = document.querySelectorAll(".reveal");
    if ("IntersectionObserver" in window) {
        const io = new IntersectionObserver(
            (es) => es.forEach((x) => {
                if (x.isIntersecting) { x.target.classList.add("in"); io.unobserve(x); }
            }), { threshold: 0.08 }
        );
        els.forEach((el) => io.observe(el));
    } else {
        els.forEach((el) => el.classList.add("in"));
    }

    // Apply buttons on dashboard / jobs pages
    document.querySelectorAll('.apply-btn').forEach((b) => {
        b.addEventListener('click', async () => {
            try {
                await window.api(b.dataset.url, 'POST');
                b.textContent = 'Applied';
                b.classList.add('applied');
                b.disabled = true;
                window.toast('Application submitted.');
            } catch (err) {
                window.toast(err.data && err.data.message ? err.data.message : err.message);
            }
        });
    });
})();