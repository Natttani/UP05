document.addEventListener('DOMContentLoaded', function () {
    const slider = document.getElementById('slider');
    if (slider) {
        const slides = Array.from(slider.querySelectorAll('.slider-slide'));
        const dotsWrap = document.getElementById('slider-dots');
        let index = 0, timer = null;
        const INTERVAL = 3000;

        slides.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'slider-dot' + (i === 0 ? ' is-active' : '');
            dot.setAttribute('aria-label', 'Слайд ' + (i + 1));
            dot.addEventListener('click', () => { goTo(i); restart(); });
            dotsWrap.appendChild(dot);
        });
        const dots = Array.from(dotsWrap.children);

        function render() {
            slides.forEach((s, i) => s.classList.toggle('is-active', i === index));
            dots.forEach((d, i) => d.classList.toggle('is-active', i === index));
        }
        function goTo(i) { index = (i + slides.length) % slides.length; render(); }
        function next() { goTo(index + 1); }
        function prev() { goTo(index - 1); }
        function start() { timer = setInterval(next, INTERVAL); }
        function stop() { if (timer) { clearInterval(timer); timer = null; } }
        function restart() { stop(); start(); }

        slider.querySelector('.slider-next').addEventListener('click', () => { next(); restart(); });
        slider.querySelector('.slider-prev').addEventListener('click', () => { prev(); restart(); });
        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);
        render(); start();
    }

    const cb = document.getElementById('other_service');
    const wrap = document.getElementById('other_service_wrap');
    if (cb && wrap) {
        const toggle = () => { wrap.hidden = !cb.checked; };
        cb.addEventListener('change', toggle);
        toggle();
    }

    document.querySelectorAll('.toast').forEach(function (toast) {
        setTimeout(function () { toast.classList.add('toast-hide'); }, 3500);
    });

    const pulseId = new URLSearchParams(location.search).get('status_changed_id');
    if (pulseId) {
        document.querySelectorAll('.badge-status-' + pulseId).forEach(function (b) {
            b.classList.add('pulse');
        });
    }

    document.querySelectorAll('.field').forEach(function (field) {
        if (field.querySelector('.error')) {
            field.querySelectorAll('input, select, textarea').forEach(function (el) {
                el.classList.add('is-error');
            });
        }
    });
});