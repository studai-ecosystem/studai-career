{{-- Custom cursor — include once per layout before </body> --}}
<style>
    *, *::before, *::after { cursor: none !important; }
    #cursor-dot {
        width: 10px; height: 10px;
        background: #2D6CDF;
        border-radius: 50%;
        position: fixed; top: 0; left: 0;
        pointer-events: none; z-index: 99999;
        transform: translate(-50%, -50%);
        transition: background .2s ease, transform .1s ease, box-shadow .2s ease;
        box-shadow: none;
    }
    #cursor-ring {
        width: 38px; height: 38px;
        border: 1.5px solid rgba(129,140,248,.6);
        border-radius: 50%;
        position: fixed; top: 0; left: 0;
        pointer-events: none; z-index: 99998;
        transform: translate(-50%, -50%);
        transition: width .2s ease, height .2s ease, border-color .2s ease;
        backdrop-filter: blur(1px);
    }
    #cursor-trail {
        width: 6px; height: 6px;
        background: rgba(20, 71, 186,.7);
        border-radius: 50%;
        position: fixed; top: 0; left: 0;
        pointer-events: none; z-index: 99997;
        transform: translate(-50%, -50%);
        box-shadow: none;
    }
</style>

<div id="cursor-dot"></div>
<div id="cursor-ring"></div>
<div id="cursor-trail"></div>

<script>
(function () {
    const dot   = document.getElementById('cursor-dot');
    const ring  = document.getElementById('cursor-ring');
    const trail = document.getElementById('cursor-trail');
    if (!dot || !ring || !trail) return;

    let mx = 0, my = 0, tx = 0, ty = 0;

    document.addEventListener('mousemove', e => {
        mx = e.clientX; my = e.clientY;
        dot.style.left  = mx + 'px';
        dot.style.top   = my + 'px';
        ring.style.left = mx + 'px';
        ring.style.top  = my + 'px';
    });

    function animateTrail() {
        tx += (mx - tx) * 0.12;
        ty += (my - ty) * 0.12;
        trail.style.left = tx + 'px';
        trail.style.top  = ty + 'px';
        requestAnimationFrame(animateTrail);
    }
    animateTrail();

    function onEnter() {
        dot.style.transform  = 'translate(-50%,-50%) scale(2.2)';
        dot.style.background = '#2D6CDF';
        dot.style.boxShadow  = '0 0 16px 5px rgba(232,121,249,.9), 0 0 40px 12px rgba(232,121,249,.4)';
        ring.style.width     = '55px';
        ring.style.height    = '55px';
        ring.style.borderColor = 'rgba(232,121,249,.7)';
    }
    function onLeave() {
        dot.style.transform  = 'translate(-50%,-50%) scale(1)';
        dot.style.background = '#2D6CDF';
        dot.style.boxShadow  = '0 0 12px 3px rgba(129,140,248,.9), 0 0 30px 8px rgba(129,140,248,.4)';
        ring.style.width     = '38px';
        ring.style.height    = '38px';
        ring.style.borderColor = 'rgba(129,140,248,.6)';
    }

    // Initial attach
    document.querySelectorAll('a, button, input, select, textarea, [role="button"], label').forEach(el => {
        el.addEventListener('mouseenter', onEnter);
        el.addEventListener('mouseleave', onLeave);
    });

    // Re-attach for dynamically added elements (Livewire / Alpine)
    const observer = new MutationObserver(() => {
        document.querySelectorAll('a, button, input, select, textarea, [role="button"], label').forEach(el => {
            if (!el.__cursorBound) {
                el.__cursorBound = true;
                el.addEventListener('mouseenter', onEnter);
                el.addEventListener('mouseleave', onLeave);
            }
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });
})();
</script>
