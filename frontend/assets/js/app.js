document.addEventListener('DOMContentLoaded', function () {
    const cb = document.getElementById('other_service');
    const wrap = document.getElementById('other_service_wrap');
    if (!cb || !wrap) return;
    const toggle = () => { wrap.hidden = !cb.checked; };
    cb.addEventListener('change', toggle);
    toggle();
});

document.addEventListener('DOMContentLoaded', function () {
    const slider = document.getElementById('slider');
    if (slider) {
        const slides = Array.from(slider.querySelectorAll('.slider-slide'));
        const dotsWrap = document.getElementById('slider-dots');
        let index = 0;
        let timer = null;
        const INTERVAL = 3000; // 3 секунды по ТЗ

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
        function stop()  { if (timer) { clearInterval(timer); timer = null; } }
        function restart() { stop(); start(); }

        slider.querySelector('.slider-next').addEventListener('click', () => { next(); restart(); });
        slider.querySelector('.slider-prev').addEventListener('click', () => { prev(); restart(); });
        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);

        render();
        start();
    }

    const cb = document.getElementById('other_service');
    const wrap = document.getElementById('other_service_wrap');
    if (cb && wrap) {
        const toggle = () => { wrap.hidden = !cb.checked; };
        cb.addEventListener('change', toggle);
        toggle();
    }

    document.querySelectorAll('input[data-phone]').forEach(function (input) {
        input.addEventListener('input', function () {
            let digits = input.value.replace(/\D/g, '');
            if (digits.startsWith('7')) digits = '8' + digits.slice(1);
            if (!digits.startsWith('8')) digits = '8' + digits;
            digits = digits.slice(0, 11);

            let out = '8';
            if (digits.length > 1) out += '(' + digits.slice(1, 4);
            if (digits.length >= 4) out += ')';
            if (digits.length > 4) out += digits.slice(4, 7);
            if (digits.length > 7) out += '-' + digits.slice(7, 9);
            if (digits.length > 9) out += '-' + digits.slice(9, 11);
            input.value = out;
        });
    });
});