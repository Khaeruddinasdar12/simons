        :root {
            --ink: #202124;
            --forest: #137333;
            --muted: #5f6368;
            --line: #e8eaed;
            --soft: #f8f9fa;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, Roboto, 'Segoe UI', sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: var(--ink);
            background: #fff;
            min-height: 100vh;
            overflow-x: clip;
        }
        .shell { width: min(1080px, calc(100% - 1.75rem)); margin: 0 auto; }
        @include('permohonan.partials.public-nav-css')
        .hero { padding: 1.2rem 0 1.25rem; }
        .hero-kicker {
            margin: 0 0 .35rem;
            font-size: .8125rem;
            font-weight: 500;
            color: var(--muted);
        }
        .hero h1 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: -.015em;
            line-height: 1.3;
        }
        .hero p {
            margin: .5rem 0 0;
            max-width: 36rem;
            font-size: 1rem;
            line-height: 1.55;
            color: var(--muted);
        }
        .form-shell { margin-bottom: 3rem; padding-top: .15rem; }
        .steps {
            margin: 0 0 1.25rem;
            font-size: .9375rem;
            color: var(--muted);
            line-height: 1.55;
        }
        .section-title {
            margin: 0 0 .9rem;
            font-size: 1rem;
            font-weight: 500;
        }
        .grid { display: grid; gap: 1rem; }
        @media (min-width: 720px) {
            .grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        input[type="file"].field { max-width: 100%; }
        .ts-wrapper { max-width: 100%; }
        .field {
            width: 100%;
            border: 1px solid var(--line);
            background: var(--soft);
            border-radius: .5rem;
            padding: .85rem .95rem;
            min-height: 3rem;
            outline: none;
            font: inherit;
            font-size: 16px;
        }
        .field:focus {
            background: #fff;
            border-color: var(--forest);
            box-shadow: 0 0 0 3px rgba(19, 115, 51, .12);
        }
        .label {
            display: block;
            font-size: .8125rem;
            font-weight: 500;
            margin-bottom: .35rem;
            color: var(--ink);
        }
        .error { color: #c5221f; font-size: .8rem; margin-top: .25rem; }
        .divider {
            height: 1px;
            background: var(--line);
            margin: 1.75rem 0;
        }
        .actions {
            display: flex;
            flex-direction: column-reverse;
            gap: 1rem;
            align-items: stretch;
        }
        .actions .btn { width: 100%; }
        @media (min-width: 640px) {
            .actions {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
            .actions .btn { width: auto; }
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: .6rem;
            padding: .9rem 1.05rem;
            min-height: 3rem;
            font: inherit;
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
            cursor: pointer;
            background: var(--forest);
        }
        .btn:hover { filter: brightness(1.05); }
        .btn:disabled { opacity: .7; cursor: wait; filter: none; }
        .alert {
            margin-bottom: 1rem;
            border-radius: .5rem;
            padding: .8rem .95rem;
            font-size: .9rem;
        }
        .alert-ok { background: #e6f4ea; color: #137333; }
        .alert-err { background: #fce8e6; color: #c5221f; }
        .ts-wrapper.single .ts-control {
            border: 1px solid var(--line);
            border-radius: .5rem;
            padding: .78rem .9rem;
            background: var(--soft);
            font: inherit;
            font-size: 16px;
            box-shadow: none;
            min-height: 3rem;
        }
        .ts-wrapper.single.focus .ts-control {
            background: #fff;
            border-color: var(--forest);
            box-shadow: 0 0 0 3px rgba(19, 115, 51, .12);
        }
        .ts-dropdown {
            border: 1px solid var(--line);
            border-radius: .5rem;
            box-shadow: 0 4px 16px rgba(32, 33, 36, .12);
            overflow: hidden;
        }
        .ts-dropdown .option.active,
        .ts-dropdown .option:hover {
            background: var(--soft);
            color: var(--ink);
        }
        .ts-wrapper.single .ts-control input { font: inherit; }
        .hint {
            margin: .4rem 0 0;
            font-size: .8rem;
            color: var(--muted);
            line-height: 1.45;
        }
        .nim-block { margin-bottom: .25rem; }
        .nim-row {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            align-items: stretch;
        }
        .nim-row .field {
            flex: 1 1 14rem;
            min-width: 0;
        }
        .btn-check {
            flex: 0 0 auto;
            white-space: nowrap;
            min-height: 3rem;
            padding: .85rem 1.15rem;
        }
        @media (max-width: 639px) {
            .nim-row { flex-direction: column; }
            .btn-check { width: 100%; }
        }
        @media (min-width: 720px) {
            .shell { width: min(1080px, calc(100% - 2rem)); }
            .hero h1 { font-size: 1.6rem; font-weight: 500; }
            .actions .btn { width: auto; min-height: 0; padding: .55rem 1.05rem; font-size: .875rem; font-weight: 500; border-radius: .375rem; }
            .btn { min-height: 2.5rem; padding: .65rem 1.05rem; font-size: .9rem; }
            .field { min-height: 2.6rem; }
            .ts-wrapper.single .ts-control { min-height: 2.6rem; }
        }
        .readonly-grid {
            display: grid;
            gap: .85rem 1.5rem;
            padding: .25rem 0 0;
        }
        @media (min-width: 720px) {
            .readonly-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        .readonly-item dt {
            font-size: .75rem;
            font-weight: 600;
            color: var(--muted);
            margin: 0 0 .15rem;
        }
        .readonly-item dd {
            margin: 0;
            font-size: .95rem;
            line-height: 1.45;
            overflow-wrap: anywhere;
        }
        .readonly-item.span-2 { grid-column: 1 / -1; }
        #form-lanjutan[hidden] { display: none !important; }
