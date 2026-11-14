/* Recruiter pages: toast, logout, API fetch helper, scroll-reveal. */
(() => {
    const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

    window.toast = (msg) => {
        const t = document.getElementById("toast");
        t.textContent = msg;
        t.classList.add("show");
        clearTimeout(window.toast.h);
        window.toast.h = setTimeout(() => t.classList.remove("show"), 2400);
    };

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

    document.getElementById("logout").addEventListener("click", async (e) => {
        e.preventDefault();
        try {
            await fetch("/recruiter/logout", {
                method: "POST",
                headers: { Accept: "application/json", "X-CSRF-TOKEN": csrf() },
            });
        } catch (_) {}
        location.href = "/recruiter/login";
    });

    // Unverified recruiters can browse but not act. (The real lock is the recruiter.verified middleware on the server.)
    if (window.RECRUITER_VERIFIED === false) {
        const ACTION = "[data-a], [data-next], #newBtn, #send, #save";
        const block = (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();
            window.toast("Verify your company first to use this action.");
        };
        document.addEventListener(
            "click",
            (e) => {
                if (e.target.closest(ACTION)) block(e);
            },
            true,
        );
        document.addEventListener(
            "submit",
            (e) => {
                if (e.target.id === "jobForm") block(e);
            },
            true,
        );
    }

    const els = document.querySelectorAll(".reveal");
    if ("IntersectionObserver" in window) {
        const io = new IntersectionObserver(
            (es) =>
                es.forEach((x) => {
                    if (x.isIntersecting) {
                        x.target.classList.add("in");
                        io.unobserve(x.target);
                    }
                }),
            { threshold: 0.08 },
        );
        els.forEach((el) => io.observe(el));
    } else els.forEach((el) => el.classList.add("in"));
})();
