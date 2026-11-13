<div class="modal" id="apply-cv-modal" role="dialog" aria-modal="true" aria-labelledby="apply-cv-title" hidden>
    <div class="sheet">
        <h3 id="apply-cv-title">Choose a CV template</h3>
        <p>Select the CV format you want to send with this application.</p>

        <div class="cv-template-options" role="radiogroup" aria-label="CV template">
            <button type="button" class="cv-template-option on" data-template="classic" role="radio" aria-checked="true">
                <strong>Classic</strong>
                <span>Clean and professional</span>
            </button>
            <button type="button" class="cv-template-option" data-template="modern" role="radio" aria-checked="false">
                <strong>Modern</strong>
                <span>Visual banner and timeline</span>
            </button>
            <button type="button" class="cv-template-option" data-template="europass" role="radio" aria-checked="false">
                <strong>Europass</strong>
                <span>Formal and structured</span>
            </button>
        </div>

        <div class="row">
            <button class="btn ghost sm" type="button" data-apply-cv-cancel>Cancel</button>
            <button class="btn primary sm" type="button" data-apply-cv-submit>Apply with this CV</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const modal = document.getElementById('apply-cv-modal');
    if (!modal) return;

    const options = [...modal.querySelectorAll('.cv-template-option')];
    const submit = modal.querySelector('[data-apply-cv-submit]');
    let activeButton = null;
    let selected = 'classic';

    const close = () => {
        modal.classList.remove('open');
        modal.hidden = true;
        activeButton = null;
    };

    const choose = (template) => {
        selected = template;
        options.forEach((option) => {
            const active = option.dataset.template === template;
            option.classList.toggle('on', active);
            option.setAttribute('aria-checked', active ? 'true' : 'false');
        });
    };

    document.addEventListener('click', (event) => {
        const button = event.target.closest('.apply-btn');
        if (!button || button.disabled) return;

        event.preventDefault();
        event.stopPropagation();
        activeButton = button;
        choose('classic');
        modal.hidden = false;
        modal.classList.add('open');
        options[0]?.focus();
    }, true);

    options.forEach((option) => {
        option.addEventListener('click', () => choose(option.dataset.template));
    });

    modal.querySelector('[data-apply-cv-cancel]').addEventListener('click', close);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) close();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('open')) close();
    });

    submit.addEventListener('click', async () => {
        if (!activeButton) return;

        submit.disabled = true;
        try {
            await window.api(activeButton.dataset.url, 'POST', { cv_template: selected });
            activeButton.textContent = 'Applied';
            activeButton.classList.add('applied');
            activeButton.disabled = true;
            document.dispatchEvent(new CustomEvent('application:submitted', {
                detail: {
                    jobId: activeButton.dataset.jobId,
                    jobTitle: activeButton.dataset.jobTitle || 'New application',
                },
            }));
            close();
            window.toast('Application submitted with your selected CV.');
        } catch (err) {
            window.toast(err.data && err.data.message ? err.data.message : err.message);
        } finally {
            submit.disabled = false;
        }
    });
})();
</script>
@endpush
