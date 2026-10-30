/* Shared by all recruiter pages: toast, logout, scroll-reveal.
   The sidebar and company details are rendered by Blade, so nothing is fetched here. */
(() => {
    const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

    window.toast = msg => {
        const t = document.getElementById('toast');
        t.textContent = msg; t.classList.add('show');
        clearTimeout(window.toast.h);
        window.toast.h = setTimeout(() => t.classList.remove('show'), 2400);
    };

    document.getElementById('logout').addEventListener('click', async e => {
        e.preventDefault();
        try { await fetch('/recruiter/logout', { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() } }); } catch (_) {}
        location.href = '/recruiter/login';
    });

    const els = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver(es => es.forEach(x => { if (x.isIntersecting) { x.target.classList.add('in'); io.unobserve(x.target); } }), { threshold: .08 });
        els.forEach(el => io.observe(el));
    } else els.forEach(el => el.classList.add('in'));
})();
