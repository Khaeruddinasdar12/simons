        .label-desk { display: none; }
        .label-mob { display: inline; }
        .nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .5rem 1rem;
            padding: .75rem 0;
            margin-bottom: .15rem;
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--line);
        }
        .logo-pair {
            display: flex;
            align-items: center;
            gap: .55rem;
            min-width: 0;
            flex: 1 1 0;
            text-decoration: none;
            color: inherit;
        }
        .logo-pair img {
            height: 2.1rem;
            width: auto;
            object-fit: contain;
            flex-shrink: 0;
        }
        .nav-brand {
            display: flex;
            flex-direction: column;
            gap: .05rem;
            min-width: 0;
        }
        .nav-brand strong {
            font-size: .9375rem;
            font-weight: 500;
            color: var(--ink);
            letter-spacing: -.01em;
            line-height: 1.2;
        }
        .nav-brand span { display: none; }
        .nav-toggle {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            margin: 0;
            pointer-events: none;
        }
        .nav-burger {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            min-height: 2.75rem;
            padding: .35rem .7rem .35rem 2.35rem;
            border: 1px solid var(--line);
            border-radius: .5rem;
            background: #fff;
            cursor: pointer;
            flex-shrink: 0;
        }
        .nav-burger em {
            font-style: normal;
            font-size: .8125rem;
            font-weight: 600;
            color: var(--ink);
        }
        .nav-burger span {
            position: absolute;
            left: .7rem;
            width: 1rem;
            height: 1.5px;
            background: var(--ink);
            border-radius: 999px;
            transition: transform .2s ease, opacity .15s ease, top .2s ease;
        }
        .nav-burger span:nth-child(1) { top: .85rem; }
        .nav-burger span:nth-child(2) { top: 1.2rem; }
        .nav-burger span:nth-child(3) { top: 1.55rem; }
        .nav-toggle:focus-visible + .nav-burger,
        .nav-burger:focus-visible {
            outline: 2px solid var(--forest);
            outline-offset: 2px;
        }
        .nav-toggle:checked + .nav-burger span:nth-child(1) {
            top: 1.2rem;
            transform: rotate(45deg);
        }
        .nav-toggle:checked + .nav-burger span:nth-child(2) { opacity: 0; }
        .nav-toggle:checked + .nav-burger span:nth-child(3) {
            top: 1.2rem;
            transform: rotate(-45deg);
        }
        .nav-panel {
            display: none;
            flex-direction: column;
            width: 100%;
            gap: .4rem;
            padding: .55rem 0 .85rem;
        }
        .nav-links {
            display: flex;
            flex-direction: column;
            gap: .15rem;
        }
        .nav-links a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--ink);
            font-weight: 500;
            font-size: 1rem;
            padding: .85rem .75rem;
            border-radius: .5rem;
            min-height: 3rem;
            background: var(--soft);
        }
        .nav-links a.primary {
            color: #fff;
            background: var(--forest);
        }
        .nav-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: 500;
            font-size: .875rem;
            color: var(--muted);
            background: transparent;
            border: 0;
            padding: .65rem .5rem;
            min-height: 2.5rem;
        }
        .nav-toggle:checked ~ .nav-panel { display: flex; }
        @media (min-width: 640px) {
            .logo-pair img { height: 2.4rem; }
            .nav-brand span {
                display: block;
                font-size: .6875rem;
                color: var(--muted);
                line-height: 1.3;
                font-weight: 400;
            }
        }
        @media (min-width: 1024px) {
            .label-desk { display: inline; }
            .label-mob { display: none; }
            .nav { flex-wrap: nowrap; padding: .7rem 0; }
            .logo-pair { flex: 0 1 auto; }
            .logo-pair img { height: 2.5rem; }
            .nav-burger { display: none; }
            .nav-panel {
                display: flex;
                flex-direction: row;
                align-items: center;
                width: auto;
                gap: 1.25rem;
                padding: 0;
            }
            .nav-links {
                flex-direction: row;
                align-items: center;
                gap: .1rem;
            }
            .nav-links a {
                padding: .45rem .7rem;
                min-height: 0;
                white-space: nowrap;
                font-size: .875rem;
                background: transparent;
                color: var(--muted);
            }
            .nav-links a.primary {
                color: var(--forest);
                background: transparent;
                box-shadow: inset 0 -2px 0 var(--forest);
                border-radius: 0;
            }
            .nav-login {
                border: 1px solid var(--line);
                border-radius: .375rem;
                padding: .4rem .85rem;
                min-height: 0;
                white-space: nowrap;
                color: var(--ink);
            }
        }
