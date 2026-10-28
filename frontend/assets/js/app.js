document.addEventListener('DOMContentLoaded', function () {
    const cb = document.getElementById('other_service');
    const wrap = document.getElementById('other_service_wrap');
    if (!cb || !wrap) return;
    const toggle = () => { wrap.hidden = !cb.checked; };
    cb.addEventListener('change', toggle);
    toggle();
});