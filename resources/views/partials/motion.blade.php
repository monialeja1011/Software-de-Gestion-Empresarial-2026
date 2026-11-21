<style>
    [x-cloak] { display: none !important; }

    @keyframes sge-rise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
    @keyframes sge-fade { from { opacity: 0; } to { opacity: 1; } }
    @keyframes sge-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    @keyframes sge-drift { 0%, 100% { transform: translate3d(0, 0, 0) scale(1); } 50% { transform: translate3d(34px, -26px, 0) scale(1.08); } }
    @keyframes sge-grow { 0%, 100% { transform: scaleY(.35); } 50% { transform: scaleY(1); } }
    @keyframes sge-stretch { 0%, 100% { transform: scaleX(.35); } 50% { transform: scaleX(1); } }
    @keyframes sge-sheen { to { background-position: 200% center; } }
    @keyframes sge-pulse { 0% { box-shadow: 0 0 0 0 rgb(var(--brand-400) / .55); } 70%, 100% { box-shadow: 0 0 0 9px rgb(var(--brand-400) / 0); } }
    @keyframes sge-shake { 10%, 90% { transform: translateX(-1px); } 20%, 80% { transform: translateX(2px); } 30%, 50%, 70% { transform: translateX(-4px); } 40%, 60% { transform: translateX(4px); } }

    .sge-rise { opacity: 0; animation: sge-rise .75s cubic-bezier(.2, .7, .2, 1) forwards; animation-delay: var(--d, 0ms); }
    .sge-fade { opacity: 0; animation: sge-fade .9s ease forwards; animation-delay: var(--d, 0ms); }
    .sge-float { animation: sge-float 7s ease-in-out infinite; animation-delay: var(--d, 0ms); }
    .sge-drift { animation: sge-drift 18s ease-in-out infinite; }
    .sge-bar { transform-origin: bottom; animation: sge-grow 3.4s ease-in-out infinite; animation-delay: var(--d, 0ms); }
    .sge-line { transform-origin: left; animation: sge-stretch 4.2s ease-in-out infinite; animation-delay: var(--d, 0ms); }
    .sge-dot { animation: sge-pulse 2.2s ease-out infinite; }
    .sge-shake { animation: sge-shake .5s; }

    .sge-shine-dark,
    .sge-shine-light {
        background-size: 200% auto;
        background-repeat: repeat-x;
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        -webkit-text-fill-color: transparent;
        animation: sge-sheen 7s linear infinite;
    }
    .sge-shine-dark { background-image: linear-gradient(90deg, rgb(var(--brand-200)), rgb(var(--brand-50)), rgb(var(--brand-300)), rgb(var(--brand-200))); }
    .sge-shine-light { background-image: linear-gradient(90deg, rgb(var(--brand-700)), rgb(var(--brand-400)), rgb(var(--brand-800)), rgb(var(--brand-700))); }

    .sge-reveal { opacity: 0; transform: translateY(22px); transition: opacity .7s ease, transform .7s cubic-bezier(.2, .7, .2, 1); transition-delay: var(--d, 0ms); }
    .sge-reveal.is-visible { opacity: 1; transform: none; }

    @media (prefers-reduced-motion: no-preference) { html { scroll-behavior: smooth; } }

    @media (prefers-reduced-motion: reduce) {
        .sge-rise, .sge-fade, .sge-float, .sge-drift, .sge-bar, .sge-line, .sge-dot, .sge-shake, .sge-shine-dark, .sge-shine-light {
            animation: none !important;
            opacity: 1 !important;
            transform: none !important;
        }
        .sge-reveal { opacity: 1; transform: none; transition: none; }
    }
</style>