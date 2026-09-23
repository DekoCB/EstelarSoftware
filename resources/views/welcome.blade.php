    <!DOCTYPE html>
    <html lang="es">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESTELAR - Software Empresarial</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="img/logo_final.png">
    <link rel="shortcut icon" type="image/png" href="img/logo_final.png">
    <link rel="apple-touch-icon" href="img/logo_final.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&family=Barlow+Condensed:wght@700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/neuropol-x" rel="stylesheet">
    <link rel="preconnect" href="https://api.fontshare.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=general-sans@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Michroma&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
    :root{
    --bg:#060912;
    --blue-deep:#0d1b3e;
    --blue-corp:#1a3a7c;
    --blue-mid:#2557cc;
    --blue-light:#5b9bd5;
    --blue-glow:#73c6f5;
    --white:#ffffff;
    --text-muted:#7a8fa8;
    --text-dim:#4a5a70;
    --gold:#c9a84c;
    --gold-light:#f0d080;
    --border:rgba(91,155,213,.18);
    --border-glow:rgba(115,198,245,.40);
    --surface:rgba(6,12,24,.78);
    --card-bg:rgba(10,18,36,.72);
    }
    html{scroll-behavior:smooth}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--white);overflow-x:hidden;min-height:100vh;-webkit-font-smoothing:antialiased}
    ::selection{background:rgba(115,198,245,.2);color:var(--white)}
    ::-webkit-scrollbar{width:6px}
    ::-webkit-scrollbar-track{background:transparent}
    ::-webkit-scrollbar-thumb{background:var(--blue-mid);border-radius:4px}
    ::-webkit-scrollbar-thumb:hover{background:var(--blue-glow)}
    /* BG LAYERS */
    .bg-stars{position:fixed;inset:0;z-index:0;pointer-events:none}
    canvas#particles-canvas{position:fixed;inset:0;z-index:1;pointer-events:none}
    .bg-grid{position:fixed;inset:0;z-index:0;opacity:.18;background-image:linear-gradient(rgba(115,198,245,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(115,198,245,.06) 1px,transparent 1px);background-size:90px 90px;animation:gridPan 60s linear infinite;pointer-events:none}
    @keyframes gridPan{from{background-position:0 0}to{background-position:90px 90px}}
    .main-container{position:relative;z-index:2}
    /* NAV */
    nav{position:fixed;top:0;width:100%;z-index:1000;padding:1rem 0;background:rgba(6,9,18,.62);backdrop-filter:blur(28px);border-bottom:1px solid rgba(115,198,245,.08);transition:all .3s ease}
    nav.scrolled{background:rgba(6,9,18,.94);box-shadow:0 18px 55px rgba(0,0,0,.35)}
    .nav-inner{max-width:1280px;margin:0 auto;padding:0 2rem;display:flex;justify-content:space-between;align-items:center}
    .logo-wrap{display:flex;align-items:center;gap:14px;text-decoration:none}
    .logo-icon{width:48px;height:48px;background:transparent;border-radius:0;display:flex;align-items:center;justify-content:center;flex-shrink:0;padding:0;animation:logoGlow 3.5s ease-in-out infinite}
    @keyframes logoGlow{0%,100%{filter:drop-shadow(0 0 4px rgba(0,200,195,.6)) drop-shadow(0 0 8px rgba(37,87,204,.35))}50%{filter:drop-shadow(0 0 14px rgba(0,228,215,.92)) drop-shadow(0 0 28px rgba(0,180,200,.5)) drop-shadow(0 0 44px rgba(37,87,204,.28))}}
    .logo-text-wrap{display:flex;flex-direction:column;line-height:1.1}
    .logo-name{font-family:'Orbitron',sans-serif;font-size:1.2rem;font-weight:900;letter-spacing:4px;background:linear-gradient(135deg,#fff 0%,var(--blue-glow) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
    .logo-sub{font-family:'Orbitron',sans-serif;font-size:.42rem;letter-spacing:3px;color:var(--blue-light);font-weight:400}
    .nav-links{display:flex;align-items:center;gap:1.75rem;list-style:none}
    .nav-links a{font-family:'Inter',sans-serif;font-size:.75rem;font-weight:600;letter-spacing:1px;color:rgba(255,255,255,.78);text-decoration:none;text-transform:uppercase;transition:color .25s ease,transform .25s ease;position:relative}
    .nav-links a::after{content:'';position:absolute;bottom:-6px;left:0;width:0;height:2px;background:var(--blue-light);transition:width .25s ease}
    .nav-links a:hover{color:var(--white);transform:translateY(-1px)}
    .nav-links a:hover::after{width:100%}
    .btn-nav{font-family:'Inter',sans-serif;font-size:.75rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.8rem 1.6rem;background:linear-gradient(180deg,rgba(0,120,255,.28),rgba(0,65,200,.42));color:white;border:1px solid rgba(0,190,255,.62);border-radius:6px;text-decoration:none;transition:all .3s ease;box-shadow:0 0 18px rgba(0,165,255,.42),0 0 35px rgba(0,100,220,.15)}
    .btn-nav:hover{background:linear-gradient(180deg,rgba(0,175,255,.42),rgba(0,100,230,.58));box-shadow:0 0 30px rgba(0,210,255,.62);border-color:rgba(0,235,255,.88);transform:translateY(-2px)}
    .mobile-toggle{display:none;background:none;border:none;color:white;font-size:1.4rem;cursor:pointer}/* HERO */
    /* HERO — reglas del carrusel retiradas: lo sustituye la composicion Logo wall (ver bloque VORTEX HERO al final de esta hoja). */
    .btn-primary{font-family:'Inter',sans-serif;font-size:.82rem;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;padding:.9rem 2.4rem;background:linear-gradient(180deg,rgba(0,130,255,.28),rgba(0,65,200,.42));color:white;border:1px solid rgba(0,195,255,.68);border-radius:6px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .3s ease;box-shadow:0 0 22px rgba(0,175,255,.48),0 0 45px rgba(0,100,220,.2),inset 0 1px 0 rgba(255,255,255,.1);cursor:pointer}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 0 35px rgba(0,220,255,.68),0 0 65px rgba(0,150,255,.32);border-color:rgba(0,235,255,.92);background:linear-gradient(180deg,rgba(0,155,255,.38),rgba(0,85,220,.52))}
    .btn-ghost{font-family:'Inter',sans-serif;font-size:.82rem;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;padding:.9rem 2.4rem;background:rgba(0,40,85,.35);color:var(--white);border:1px solid rgba(0,175,255,.45);border-radius:6px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .3s ease;backdrop-filter:blur(12px);box-shadow:0 0 15px rgba(0,150,255,.2),inset 0 1px 0 rgba(255,255,255,.05);cursor:pointer}
    .btn-ghost:hover{background:rgba(0,95,200,.25);border-color:rgba(0,220,255,.72);box-shadow:0 0 28px rgba(0,180,255,.38);transform:translateY(-2px)}
    /* STATS BAR */
    .stats-section{background:linear-gradient(180deg,rgba(6,12,24,.84),rgba(6,12,24,.74));border-top:1px solid rgba(115,198,245,.18);border-bottom:1px solid rgba(115,198,245,.18);backdrop-filter:blur(28px);padding:2rem;box-shadow:0 0 45px rgba(37,87,204,.12)}
    .stats-inner{max-width:1280px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);gap:2rem;text-align:center}
    .stat-item{position:relative}
    .stat-item:not(:last-child)::after{content:'';position:absolute;right:0;top:10%;height:80%;width:1px;background:var(--border)}
    .stat-num{font-family:'Orbitron',sans-serif;font-size:2.2rem;font-weight:900;background:linear-gradient(135deg,var(--white),var(--blue-glow));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;display:block}
    .stat-label{font-size:.8rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:2px;font-family:'Space Mono',monospace;margin-top:.25rem}
    /* SECTIONS */
    section{padding:6rem 2rem}
    .container{max-width:1280px;margin:0 auto}
    .section-label{font-family:'Space Mono',monospace;font-size:.7rem;letter-spacing:3.5px;text-transform:uppercase;color:var(--blue-light);display:inline-flex;align-items:center;gap:.4rem;margin-bottom:1rem}
    .section-title{font-family:'Inter',sans-serif;font-size:clamp(1.9rem,3.8vw,3.3rem);font-weight:800;letter-spacing:.02em;text-transform:uppercase;background:linear-gradient(135deg,#ffffff 25%,var(--blue-light) 95%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:1rem}
    .section-sub{color:var(--text-muted);font-size:1.05rem;max-width:720px;line-height:1.9}
    .section-head{margin-bottom:4rem}
    .section-head.center{text-align:center}
    .section-head.center .section-sub{margin:0 auto}/* CARDS */
    .card{background:var(--card-bg);border:1px solid var(--border);border-radius:20px;padding:1.8rem;backdrop-filter:blur(14px);transition:all .3s ease;position:relative;overflow:hidden}
    .card::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(115,198,245,.05),transparent);opacity:0;transition:opacity .3s}
    .card:hover{border-color:var(--border-glow);box-shadow:0 12px 40px rgba(37,87,204,.18);transform:translateY(-5px)}
    .card:hover::before{opacity:1}
    .card-icon{width:50px;height:50px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:1.2rem;background:linear-gradient(135deg,var(--blue-corp),var(--blue-mid));box-shadow:0 8px 20px rgba(37,87,204,.3)}
    .card-title{font-family:'Inter',sans-serif;font-size:1.05rem;font-weight:700;color:var(--white);margin-bottom:.7rem;letter-spacing:.3px}
    .card-body{color:var(--text-muted);font-size:.9rem;line-height:1.7}
    /* SPOTLIGHT CARDS (características) */
    .spotlight-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem}
    /* BOT CARDS */
    .bots-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:1.5rem}
    .bot-card{background:var(--card-bg);border:1px solid var(--border);border-radius:20px;padding:1.8rem;backdrop-filter:blur(14px);transition:all .3s ease}
    .bot-card:hover{border-color:var(--border-glow);box-shadow:0 12px 40px rgba(37,87,204,.18);transform:translateY(-5px)}
    .bot-top{display:flex;align-items:center;gap:14px;margin-bottom:1rem}
    .bot-avatar{width:50px;height:50px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;background:linear-gradient(135deg,var(--blue-corp),var(--blue-mid));box-shadow:0 8px 20px rgba(37,87,204,.3);flex-shrink:0}
    .bot-name{font-family:'Inter',sans-serif;font-weight:700;font-size:1rem;color:var(--white)}
    .bot-role{font-size:.78rem;color:var(--text-muted)}
    .bot-desc{font-size:.88rem;color:var(--text-muted);line-height:1.7;margin-bottom:1rem}
    .bot-tags{display:flex;gap:.6rem;flex-wrap:wrap}
    .bot-tag{background:rgba(37,87,204,.18);border:1px solid rgba(91,155,213,.2);border-radius:6px;padding:.25rem .65rem;font-size:.72rem;font-family:'Space Mono',monospace;color:var(--blue-light)}
    /* PLANES */
    .planes-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:2rem}
    .plan-card{background:var(--card-bg);border:1px solid var(--border);border-radius:24px;padding:2.2rem;backdrop-filter:blur(14px);position:relative;transition:all .3s ease}
    .plan-card.featured{border-color:var(--blue-glow);box-shadow:0 0 50px rgba(115,198,245,.15)}
    .plan-card:hover{transform:translateY(-4px);box-shadow:0 16px 50px rgba(37,87,204,.2)}
    .plan-popular{position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,var(--blue-mid),var(--blue-glow));color:#fff;font-family:'Orbitron',sans-serif;font-size:.6rem;font-weight:800;letter-spacing:2px;padding:5px 16px;border-radius:999px;white-space:nowrap}
    .plan-name{font-family:'Orbitron',sans-serif;font-size:.9rem;font-weight:700;color:var(--text-muted);letter-spacing:2px;text-transform:uppercase;margin-bottom:.8rem}
    .plan-price{font-family:'Orbitron',sans-serif;font-size:3rem;font-weight:900;color:var(--white);margin-bottom:.3rem}
    .plan-price sup{font-size:1.4rem;color:var(--blue-glow);vertical-align:super}
    .plan-period{font-size:.82rem;color:var(--text-muted);margin-bottom:1.8rem}
    .plan-divider{border:none;border-top:1px solid var(--border);margin-bottom:1.8rem}
    .plan-features{list-style:none;margin-bottom:2rem}
    .plan-features li{display:flex;align-items:center;gap:10px;padding:.6rem 0;font-size:.9rem;color:var(--text-muted);border-bottom:1px solid rgba(255,255,255,.03)}
    .plan-features li:last-child{border-bottom:none}
    .plan-features li i{color:var(--blue-glow);font-size:.8rem;flex-shrink:0}
    /* CTA */
    .cta-wrap{background:linear-gradient(135deg,rgba(13,27,62,.9),rgba(26,58,124,.7));border:1px solid rgba(91,155,213,.25);border-radius:28px;text-align:center;padding:5rem 2rem}
    /* CONTACT FORM */
    .contact-form{max-width:640px;margin:0 auto;background:var(--card-bg);border:1px solid var(--border);border-radius:24px;padding:2.5rem;backdrop-filter:blur(14px)}
    .form-group{margin-bottom:1.4rem}
    .form-group label{display:block;font-size:.8rem;font-weight:600;color:var(--blue-light);margin-bottom:.45rem;letter-spacing:.5px;text-transform:uppercase}
    .form-group input,.form-group textarea,.form-group select{width:100%;padding:.85rem 1rem;background:rgba(13,27,62,.6);border:1px solid var(--border);border-radius:10px;color:var(--white);font-family:'Inter',sans-serif;font-size:.9rem;transition:border-color .25s;outline:none}
    .form-group input:focus,.form-group textarea:focus,.form-group select:focus{border-color:var(--blue-glow)}
    .form-group textarea{min-height:120px;resize:vertical}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    /* FOOTER */
    footer{background:rgba(6,9,18,.9);border-top:1px solid var(--border);padding:4rem 2rem}
    .footer-inner{max-width:1280px;margin:0 auto;display:grid;grid-template-columns:1.6fr 1fr 1fr 1fr;gap:3rem}
    .footer-brand p{color:var(--text-muted);font-size:.88rem;line-height:1.7;margin-top:.9rem;max-width:280px}
    .footer-col h4{font-family:'Orbitron',sans-serif;font-size:.72rem;font-weight:700;color:var(--white);letter-spacing:2px;text-transform:uppercase;margin-bottom:1.2rem}
    .footer-col ul{list-style:none}
    .footer-col ul li a{color:var(--text-muted);text-decoration:none;font-size:.88rem;display:block;padding:.35rem 0;transition:color .2s}
    .footer-col ul li a:hover{color:var(--blue-glow)}
    .footer-bottom{max-width:1280px;margin:2.5rem auto 0;padding-top:2rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;font-size:.82rem;color:var(--text-muted);flex-wrap:wrap;gap:1rem}
    .social-links{display:flex;gap:1rem}
    .social-links a{width:36px;height:36px;border-radius:9px;background:rgba(37,87,204,.15);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-muted);text-decoration:none;transition:all .25s}
    .social-links a:hover{background:var(--blue-mid);color:white;border-color:var(--blue-glow);transform:translateY(-2px)}
    /* REVEAL */
    .reveal{opacity:0;transform:translateY(24px);transition:opacity .6s ease,transform .6s ease}
    .reveal.visible{opacity:1;transform:translateY(0)}
    /* RESPONSIVE */
    @media(max-width:1024px){.footer-inner{grid-template-columns:1fr 1fr}.stats-inner{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:768px){
    section{padding:4rem 1rem}
    .nav-links{display:none}
    .nav-links.open{display:flex;flex-direction:column;position:fixed;top:78px;left:0;right:0;background:rgba(6,9,18,.98);padding:2rem;gap:1.5rem;border-bottom:1px solid var(--border)}
    .mobile-toggle{display:block}
    .stats-inner{grid-template-columns:repeat(2,1fr)}
    .footer-inner{grid-template-columns:1fr;gap:2rem}
    .footer-bottom{flex-direction:column;text-align:center}
    .form-row{grid-template-columns:1fr}
    }
    
    
    /* ══════════════════════════════════════════════════════════════════
     * VORTEX HERO — "Logo wall" (tema oscuro)
     * Composicion Figma 2089:1572 (1920x1080) sobre la escena Three.js.
     * Medidas en PIXELES DUROS: a 1920x1080 renderiza 1:1. Nada escala de
     * forma fluida; cada breakpoint reescribe la tabla entera.
     * ══════════════════════════════════════════════════════════════════ */

    /* ── Reset ─────────────────────────────────────────────────────────
     * ACOTADO a .lk-stage. El original es global (Preflight de Tailwind),
     * pero esta pagina ya tiene 7 secciones con su propio reset; aplicarlo
     * global reventaria los h2 de .section-title y las listas del nav. */
    .lk-stage *, .lk-stage *::before, .lk-stage *::after { box-sizing: border-box; border: 0 solid }
    .lk-stage h1, .lk-stage h2, .lk-stage h3 { font-size: inherit; font-weight: inherit }
    .lk-stage p, .lk-stage h1, .lk-stage h2, .lk-stage h3, .lk-stage figure, .lk-stage blockquote { margin: 0 }
    .lk-stage b, .lk-stage strong { font-weight: bolder }
    .lk-stage small { font-size: 80% }
    .lk-stage img, .lk-stage svg, .lk-stage video, .lk-stage canvas { display: block; max-width: 100% }

    /* ── Tokens ────────────────────────────────────────────────────── */
    :root {
    --font-general: "General Sans", system-ui, sans-serif;
    --font-onest: "Onest", system-ui, sans-serif;
    /* Michroma para los titulares de seccion y el del hero. Es una display
     * ancha y de un solo peso; el resto de la pagina sigue en General Sans,
     * que es donde hay texto que leer. */
    --font-display: "Michroma", "General Sans", system-ui, sans-serif;
    --radius-pill: 999px;

    /* El suelo de la pagina. Detras corre la escena Three.js. */
    --lk-asset: #000;

    /* Cromo del tema oscuro: cristal negro, no blanco — sobre una
     * composicion oscura las placas blancas gritaban mas que el titular.
     * El pelo de 1px es lo que mantiene legible su borde. */
    --em-plate: rgba(0, 0, 0, 0.55);
    --em-plate-hover: rgba(0, 0, 0, 0.75);
    --em-plate-edge: rgba(255, 255, 255, 0.16);
    --em-plate-blur: 12px;
    --em-plate-ink: #f2f2f2;
    --em-bar: rgba(0, 0, 0, 0.55);
    --em-bar-edge: rgba(255, 255, 255, 0.16);
    }

    /* ── El escenario ──────────────────────────────────────────────────
     * ADAPTACION: el original manda `body{height:100svh;overflow:hidden}`.
     * Aqui eso mataria el scroll de las 7 secciones de abajo, asi que la
     * regla vive en .lk-stage — una pantalla de alto, sin scroll propio, y
     * la pagina sigue corriendo por debajo. */
    .lk-stage {
    position: relative;
    width: 100%;
    height: 100svh;
    min-height: 560px;
    /* La landing da `section{padding:6rem 2rem}` a todas sus secciones;
     * este hero se mide contra sus propios bordes. */
    padding: 0;
    overflow: hidden;
    background: var(--lk-asset);
    font-family: var(--font-onest), system-ui, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    }

    /* El canvas de la escena: primero en el DOM, detras de todo. */
    .lk-stage canvas#scene {
    position: absolute; inset: 0; z-index: 0;
    width: 100%; height: 100%; display: block;
    }

    /* La capa de composicion. El puntero se lo queda la escena; solo el
     * cromo interactivo lo recupera (regla mas abajo). */
    .uip-layer { position: absolute; inset: 0; display: block; z-index: 2; pointer-events: none }

    /* ── Estado de entrada ─────────────────────────────────────────────
     * Lo que entra en cascada arranca oculto para que no parpadee antes de
     * que el script tome el control. El script pone `.lk-js` en <html>, asi
     * que sin JS la regla nunca casa y la pagina se ve entera.
     *
     * `will-change` NO vive aqui a proposito: crearia un contexto de
     * apilamiento permanente en cada elemento de la cascada y atraparia el
     * z-index de lo que lleve dentro. El script lo pone inline durante el
     * vuelo y lo limpia al posarse. */
    html.lk-js .lk-stage [data-rise] { opacity: 0 }

    /* ── Primitivas de la libreria de looks (.lk*) ─────────────────── */
    .lk { overflow: hidden; color: rgba(255, 255, 255, 0.94) }
    .lk-brand { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.78rem; font-weight: 650; letter-spacing: -0.01em }
    .lk-mark { width: 0.62rem; height: 0.62rem; flex: none; border: 2px solid currentColor; border-radius: 50% }
    .lk-pill { display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; width: fit-content; padding: 0.34rem 0.8rem; border-radius: var(--radius-pill); font-size: 0.66rem; font-weight: 550; white-space: nowrap }
    .lk-pill-dark { background: rgba(13, 13, 13, 0.92); color: rgba(255, 255, 255, 0.95); font-weight: 600 }
    .lk-pill-ghost { border: 1px solid color-mix(in srgb, currentColor 40%, transparent) }

    /* Vocabulario interior compartido — el scope de abajo lo reescribe. */
    .lk .lkx-nav { position: relative; z-index: 3; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.9rem 1.2rem; font-size: 0.66rem }
    .lk .lkx-links { display: flex; align-items: center; gap: 0.85rem; font-size: 0.64rem; opacity: 0.88; white-space: nowrap }
    .lk .lkx-navend { display: flex; align-items: center; gap: 0.6rem }
    .lk .lkx-copy { position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.8rem; padding: 0 1.2rem; margin-top: 1.6rem }
    .lk .lkx-h1 { font-family: var(--font-general), system-ui, sans-serif; font-size: 2.1rem; font-weight: 600; line-height: 1.04; letter-spacing: -0.02em }
    .lk .lkx-sub { font-size: 0.7rem; line-height: 1.5; opacity: 0.78 }
    .lk .lkx-cta { display: flex; align-items: center; gap: 0.6rem }

    /* Cromo interactivo — los controles aceptan el puntero y responden al
     * instante (solo cambios de estado; el movimiento es cosa del muelle). */
    .lk .lk-pill, .lk .lkx-links a, .lk .lk-brand { pointer-events: auto; cursor: pointer }
    .lk .lk-pill:hover { filter: brightness(1.08); box-shadow: 0 0 0 2px color-mix(in srgb, currentColor 20%, transparent) }
    .lk .lk-pill:active { filter: brightness(0.92) }

    /* ══ Logo wall — la composicion ═════════════════════════════════════
     * Todo lo especifico de .lk-emerald. Las piezas van colocadas en
     * absoluto contra la pagina, no apiladas en flujo, porque asi las
     * coloca Figma: el lockup arriba a la izquierda, la barra de pildoras
     * centrada, las acciones de cuenta arriba a la derecha, el hero sobre el
     * centro vertical de la pagina, y la barra de cristal soldada al borde
     * inferior. */
    .lk-emerald {
    /* Un pelo es un pelo a cualquier tamano: queda fuera de la tabla y no
     * escala nunca con el resto. */
    --em-hair: 1px;

    /* ── El marco, 1:1 ── directo del archivo de Figma. Cada breakpoint
     * del final de esta hoja reescribe esta tabla en sus propios pixeles. */
    --em-edge: 30px;           --em-navtop: 16px;         --em-brandtop: 32px;
    --em-ui: 24px;             --em-mark: 18px;           --em-markgap: 8px;
    --em-btnh: 56px;           --em-radius: 8px;          --em-navw: 160px;
    --em-navgap: 4px;          --em-authw: 180px;         --em-authgap: 12px;
    --em-btnx: 40px;           --em-heroy: -3.5px;        --em-herow: 1260px;
    --em-herogap: 48px;        --em-h1: 61px;             --em-sub: 28px;
    --em-subw: 719px;          --em-subtop: 41.8px;       --em-ctagap: 12px;
    --em-barh: 79px;           --em-barpad: 0px;          --em-barrowgap: 0px;
    --em-logogap: 58px;        --em-logo: 28.9px;         --em-icongap: 12px;
    --em-rule: 80px;           --em-trust: 28px;          --em-trustgap: 12px;
    --em-btntext: 17px;

    font-family: var(--font-general), system-ui, sans-serif;
    color: #fff;
    }

    /* ── Figma "Frame 12" · el lockup ─────────────────────────────── */
    .lk-emerald .lk-brand {
    position: absolute; left: var(--em-edge); top: var(--em-brandtop); z-index: 5;
    display: inline-flex; align-items: center; gap: var(--em-markgap);
    height: var(--em-ui); font-size: var(--em-ui); font-weight: 400;
    line-height: 1; letter-spacing: .08em; color: #fff; text-decoration: none;
    }
    /* ADAPTACION: donde Figma pone un anillo de 18px va el isotipo de
     * ESTELAR, al mismo tamano y en la misma posicion de la reticula. */
    .lk-emerald .lk-mark {
    width: var(--em-mark); height: var(--em-mark); flex: none;
    border: 0; border-radius: 0; object-fit: contain;
    filter: drop-shadow(0 0 6px rgba(0, 210, 200, .55));
    }

    /* ── Figma "Frame 40" · la barra de pildoras ──────────────────────
     * Centrada en la pagina, no en el hueco que dejan el lockup y las
     * acciones — Figma la clava en el medio del marco (x 552 de 1920, ancho
     * 816), asi que los dos extremos crecen independientemente de ella. */
    .lk-emerald .lkx-nav {
    position: absolute; left: 50%; top: var(--em-navtop); z-index: 5;
    display: block; gap: 0; padding: 0;
    transform: translateX(-50%); font-size: var(--em-ui);
    /* Neutraliza la regla de elemento `nav{position:fixed;width:100%;
     * background:...;z-index:1000}` de la landing, que se cuela aqui por ser
     * esta barra tambien un elemento nav: sin esto mide 1920 en vez de 816,
     * se pega a la izquierda y su primera placa tapa el lockup. */
    width: auto; background: transparent; border: 0; box-shadow: none;
    -webkit-backdrop-filter: none; backdrop-filter: none;
    }
    .lk-emerald .lkx-links { display: flex; align-items: center; gap: var(--em-navgap); font-size: inherit; opacity: 1 }

    /* Cada item es una placa fija de 160x56 y la etiqueta va centrada en
     * ella, no separada de sus bordes por padding — la etiqueta mas larga es
     * mas ancha que los 80px que dejaria un padding de 40, y Figma deja que
     * sobresalga simetricamente. */
    .lk-emerald .lkx-links a {
    display: inline-flex; align-items: center; justify-content: center;
    width: var(--em-navw); height: var(--em-btnh);
    border-radius: var(--em-radius);
    background: var(--em-plate);
    box-shadow: inset 0 0 0 1px var(--em-plate-edge);
    -webkit-backdrop-filter: blur(var(--em-plate-blur));
    backdrop-filter: blur(var(--em-plate-blur));
    color: var(--em-plate-ink);
    font-size: var(--em-btntext); font-weight: 400; line-height: 1;
    white-space: nowrap; text-decoration: none;
    }
    /* Las placas son botones, no enlaces de texto: nada de subrayado al
     * hover, responde el relleno. */
    .lk-emerald .lkx-links a:hover { background: var(--em-plate-hover); text-decoration: none }

    /* ── Figma "Frame 42" · las acciones de cuenta ────────────────── */
    .lk-emerald .lkx-navend {
    position: absolute; right: var(--em-edge); top: var(--em-navtop); z-index: 5;
    display: flex; align-items: flex-start; gap: var(--em-authgap);
    }

    /* Una sola receta de boton para todo el marco — el par del nav y el par
     * del hero son la misma placa de 56px; solo cambia si el ancho es fijo
     * (nav) o lo dicta la etiqueta (hero). */
    .lk-emerald .lk-pill {
    display: inline-flex; align-items: center; justify-content: center; gap: 0;
    width: auto; height: var(--em-btnh); padding: 0 var(--em-btnx);
    border-radius: var(--em-radius); font-size: var(--em-btntext);
    font-weight: 400; line-height: 1; white-space: nowrap; text-decoration: none;
    }
    .lk-emerald .lkx-navend .lk-pill { width: var(--em-authw); padding: 0 }
    .lk-emerald .lk-pill-dark { background: #000; border: 0; color: #fff; font-weight: 400 }
    .lk-emerald .lk-pill-ghost { background: transparent; border: var(--em-hair) solid rgba(255, 255, 255, 0.5); color: #fff; font-weight: 400 }

    /* ── Figma "Frame 44" · el hero ────────────────────────────────────
     * Centrado en el medio de la pagina menos los 3.5px que carga Figma: el
     * bloque mide 425 de alto contra un marco de 1080 y va en y 324. */
    .lk-emerald .lkx-copy {
    position: absolute; left: var(--em-edge); top: calc(50% + var(--em-heroy)); z-index: 4;
    display: flex; flex-direction: column; align-items: flex-start; justify-content: center;
    gap: var(--em-herogap);
    width: var(--em-herow); max-width: calc(100% - var(--em-edge) * 2);
    margin: 0; padding: 0; transform: translateY(-50%); text-align: left;
    }
    .lk-emerald .lkx-group { width: 100% }
    .lk-emerald .lkx-h1 {
    font-family: var(--font-display);
    font-size: var(--em-h1); font-weight: 400; line-height: 1.1;
    letter-spacing: normal; text-transform: uppercase; color: #fff;
    }
    .lk-emerald .lkx-h1 span { display: block }

    /* La segunda linea la pinta un degradado recortado a los glifos: blanco
     * al 50% a la izquierda, al 20% a la derecha, a lo ancho de la medida
     * propia del titular (1260px), que es la caja que Figma llena. */
    .lk-emerald .lkx-dim {
    background-image: linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0.2) 100%);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent; color: transparent;
    }

    /* Figma apila el parrafo sobre el titular dentro de un grupo y pone su
     * borde superior en y 253 — 41.8px por debajo de las dos lineas 96/1.1. */
    .lk-emerald .lkx-sub {
    width: var(--em-subw); max-width: 100%; margin-top: var(--em-subtop);
    font-size: var(--em-sub); font-weight: 400; line-height: 1.2; color: #fff; opacity: 1;
    }
    .lk-emerald .lkx-cta { display: flex; align-items: flex-start; gap: var(--em-ctagap) }


    /* Figma "Line 4" — una regla de 80x2 cuyo trazo se desvanece al alejarse
     * de la etiqueta que introduce. */
    .lk-emerald .lkx-rule { width: var(--em-rule); height: 2px; flex: none; background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, #fff 100%) }


    /* ── Redes sociales (fijas) ───────────────────────────────────────
     * Cuatro circulos negros con el pelo del sistema, clavados a la esquina
     * inferior derecha del viewport en toda la pagina. Al pasar el puntero,
     * un destello cruza el circulo en diagonal —la "reluciente"— y el pelo
     * y un halo se encienden.
     *
     * Vive fuera del hero, colgando del body: un `position: fixed` deja de
     * serlo si un ancestro lleva transform, perspective o filter, y la
     * pagina esta llena de eso. Aqui arriba no hay nada que lo capture. Las
     * medidas salen de los tokens de pagina (--pg-*), que bajan por los
     * mismos escalones que la composicion. */
    .lk-social {
    position: fixed; right: var(--pg-gutter); bottom: var(--pg-gutter);
    /* por encima del nav fijo (1000), por debajo del cursor */
    z-index: 1001;
    display: flex; gap: 12px;
    pointer-events: auto;
    /* Es un elemento nav: la landing declara `nav{position:fixed;top:0;
     * width:100%;background:...;padding:...}` a nivel de elemento. Sin
     * esto la fila se clavaba arriba y a lo ancho, con el fondo de la barra. */
    top: auto; left: auto; width: auto; height: auto;
    padding: 0; border: 0; background: transparent; box-shadow: none;
    -webkit-backdrop-filter: none; backdrop-filter: none;
    }
    .lk-social a {
    --sz: calc(var(--pg-btnh) * 1.04);   /* 58px a 1:1; era 48 */
    position: relative; overflow: hidden;
    display: inline-flex; align-items: center; justify-content: center;
    width: var(--sz); height: var(--sz);
    border-radius: 50%;
    background: #000;
    box-shadow: inset 0 0 0 var(--pg-hair) rgba(255, 255, 255, .22);
    color: #fff; font-size: calc(var(--sz) * .42);
    text-decoration: none;
    transition: box-shadow .35s ease, transform .35s cubic-bezier(.22, 1, .36, 1);
    }
    .lk-social a i { position: relative; z-index: 1 }
    /* El destello: una banda de luz inclinada que espera fuera del circulo
     * por la izquierda y lo cruza entero al hover. */
    .lk-social a::before {
    content: ''; position: absolute; top: -60%; left: -90%;
    width: 60%; height: 220%;
    background: linear-gradient(105deg,
        rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0) 30%,
        rgba(255, 255, 255, .62) 50%,
        rgba(255, 255, 255, 0) 70%, rgba(255, 255, 255, 0) 100%);
    transform: skewX(-22deg);
    transition: left .6s cubic-bezier(.22, 1, .36, 1);
    pointer-events: none;
    }
    .lk-social a:hover::before,
    .lk-social a:focus-visible::before { left: 140% }
    .lk-social a:hover,
    .lk-social a:focus-visible {
    transform: translateY(-2px);
    box-shadow: inset 0 0 0 var(--pg-hair) rgba(255, 255, 255, .78),
                0 0 0 1px rgba(255, 255, 255, .08),
                0 0 26px rgba(255, 255, 255, .32);
    outline: none;
    }
    /* La ultima linea del footer queda bajo la fila: se le da aire para que
     * el texto de la derecha no se tape. */
    footer { padding-bottom: calc(var(--pg-sec) * .5 + var(--pg-btnh) + var(--pg-gutter)) }
    @media (prefers-reduced-motion: reduce) {
    .lk-social a::before { transition: none }
    }

    /* ══ Moneda "Eventos" (esquina inferior izquierda) ═══════════════════
     * Circulo negro que gira como moneda en reposo (rotateY en bucle),
     * con el icono de ESTELAR en blanco de cara. Al hover acelera el giro
     * 1s exacto y se detiene mostrando la CARA DE ATRAS de la moneda
     * -literal: rotateY(180deg)-, que lleva el texto "EVENTOS" con un
     * destello. Sigue siendo un <a href> real (fallback si el JS no
     * cargo); con JS el click abre el panel deslizante en vez de
     * navegar -ver el listener de click mas abajo-.
     *
     * 3D de verdad, no solo dos caras planas: las dos caras se separan a
     * lo largo del eje Z (translateZ +-espesor/2) y el hueco entre ambas
     * se rellena con el "canto" -una franja de segmentos angostos en
     * abanico alrededor del eje Y, generada en JS (buildCoinEdge)-, asi
     * que al pasar por el perfil se ve un borde real, no una linea.
     * --coin-thick lo fija buildCoinEdge() en runtime, midiendo el
     * circulo ya renderizado (por eso el fallback a 0px: antes de que
     * corra ese JS, las caras coinciden en el mismo plano sin hueco). */
    .lk-coin {
    --coin-sz: calc(var(--pg-btnh) * 1.75);
    position: fixed; left: var(--pg-gutter); bottom: var(--pg-gutter);
    z-index: 1001;
    width: var(--coin-sz); height: var(--coin-sz);
    border-radius: 50%;
    padding: 0; border: 0; background: transparent; cursor: pointer;
    perspective: 640px;
    }
    .lk-coin-spin {
    position: relative; display: block; width: 100%; height: 100%;
    transform-style: preserve-3d;
    animation: coinSpin 3.2s linear infinite;
    /* Solo se usa al asentarse: pasar de animation a un transform fijo
     * deja pendiente esta transicion, que frena el giro con una decel-
     * eracion en vez de cortarlo en seco. */
    transition: transform .4s ease-out;
    }
    .lk-coin.is-fast .lk-coin-spin { animation-name: coinSpin; animation-duration: .28s }
    .lk-coin.is-settled .lk-coin-spin { animation: none; transform: rotateY(180deg) }
    @keyframes coinSpin { to { transform: rotateY(360deg) } }

    .lk-coin-face {
    position: absolute; inset: 0; overflow: hidden; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: #000;
    box-shadow: inset 0 0 0 2px rgba(255, 255, 255, .28), 0 6px 20px rgba(0, 0, 0, .5);
    backface-visibility: hidden;
    transform: translateZ(calc(var(--coin-thick, 0px) / 2));
    }
    .lk-coin-face--a img { width: 52%; height: auto; display: block }
    .lk-coin-face--b {
    transform: rotateY(180deg) translateZ(calc(var(--coin-thick, 0px) / 2));
    color: #fff; font-family: 'Orbitron', sans-serif; font-weight: 700;
    font-size: calc(var(--coin-sz) * .155); letter-spacing: .04em; text-transform: uppercase;
    }
    /* Canto: los segmentos los agrega buildCoinEdge() por JS. */
    .lk-coin-edge {
    position: absolute; inset: 0; transform-style: preserve-3d; pointer-events: none;
    }
    .lk-coin-edge-seg {
    position: absolute; left: 50%; top: 50%;
    background: linear-gradient(90deg, #050505, #2c2c2c 48%, #050505);
    }
    /* Destello: una sola vez, justo cuando la cara de atras queda al frente. */
    .lk-coin.is-settled .lk-coin-face--b::after {
    content: ''; position: absolute; top: -60%; left: -90%; width: 60%; height: 220%;
    background: linear-gradient(105deg,
        rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0) 30%,
        rgba(255, 255, 255, .8) 50%,
        rgba(255, 255, 255, 0) 70%, rgba(255, 255, 255, 0) 100%);
    transform: skewX(-22deg);
    animation: coinShine 1s ease .28s 1;
    }
    @keyframes coinShine { from { left: -90% } to { left: 140% } }

    @media (prefers-reduced-motion: reduce) {
    .lk-coin-spin { animation: none; transition: none }
    .lk-coin.is-settled .lk-coin-face--b::after { animation: none }
    }

    /* El overlay/carrusel de Eventos (CardSwap + FlipCard) se retiro: la
     * moneda ahora es un link directo a la pagina propia del evento
     * (target="_blank"), ver #eventsCoin mas abajo y
     * resources/views/eventos/asistentes/showcase.blade.php. */

    /* ══ Las tablas de breakpoints ══════════════════════════════════════
     * Nada de lo de arriba es fluido — cada medida es un pixel, asi que una
     * ventana que no puede sentar el marco a 1:1 recibe su propio juego de
     * pixeles, no una version estirada de estos. Cada escalon reescribe la
     * tabla completa, de modo que cualquier breakpoint se lee como una hoja
     * de especificacion sin perseguir lo que heredo.
     *
     * El escalon lo elige lo que se rompe primero: la barra de pildoras
     * centrada chocando con las acciones de cuenta. La barra mide 816 y las
     * acciones 372 mas 30 de calle, ambas medidas desde el medio, lo que
     * pone el suelo en `width >= 1620 x escala`; las tablas de abajo toman
     * `1720 x escala` para que las dos nunca se toquen a ras.
     *
     * DOS escalas, no una. El cromo — placas del nav, botones, calles, la
     * barra de confianza — baja con la escala del escalon, porque se
     * dimensiona contra la ventana. El HERO baja con la suya, mas lenta
     * (--em-herow y todo lo de dentro mantienen la proporcion 1260 : 719 :
     * 96 que carga Figma), porque un titular encogido al ritmo de un boton
     * se lee como tipografia pequena en una pantalla pequena en vez de como
     * la misma composicion. */

    /* x0.84 cromo · x0.94 hero — 1512 / 1600 / 1680 — ya no es 1:1 */
    @media (max-width: 1720px) {
    .lk-emerald {
        --em-edge: 25px;           --em-navtop: 13px;         --em-brandtop: 27px;
        --em-ui: 20px;             --em-mark: 15px;           --em-markgap: 7px;
        --em-btnh: 47px;           --em-radius: 7px;          --em-navw: 134px;
        --em-navgap: 3px;          --em-authw: 151px;         --em-authgap: 10px;
        --em-btnx: 34px;           --em-heroy: -3px;          --em-herow: 1205px;
        --em-herogap: 45px;        --em-h1: 58px;             --em-sub: 26px;
        --em-subw: 688px;          --em-subtop: 39px;         --em-ctagap: 10px;
        --em-barh: 66px;           --em-barpad: 0px;          --em-barrowgap: 0px;
        --em-logogap: 49px;        --em-logo: 24.3px;         --em-icongap: 10px;
        --em-rule: 67px;           --em-trust: 24px;          --em-trustgap: 10px;
        --em-btntext: 14px;
    }
    }

    /* x0.72 cromo · x0.83 hero — 1280 / 1366 / 1440 */
    @media (max-width: 1448px) {
    .lk-emerald {
        --em-edge: 22px;           --em-navtop: 12px;         --em-brandtop: 23px;
        --em-ui: 17px;             --em-mark: 13px;           --em-markgap: 6px;
        --em-btnh: 40px;           --em-radius: 6px;          --em-navw: 115px;
        --em-navgap: 3px;          --em-authw: 130px;         --em-authgap: 9px;
        --em-btnx: 29px;           --em-heroy: -3px;          --em-herow: 1071px;
        --em-herogap: 40px;        --em-h1: 52px;             --em-sub: 23px;
        --em-subw: 611px;          --em-subtop: 35px;         --em-ctagap: 9px;
        --em-barh: 57px;           --em-barpad: 0px;          --em-barrowgap: 0px;
        --em-logogap: 42px;        --em-logo: 20.8px;         --em-icongap: 9px;
        --em-rule: 58px;           --em-trust: 20px;          --em-trustgap: 9px;
        --em-btntext: 12px;
    }
    }

    /* x0.60 cromo · x0.71 hero — 1180 / portatiles pequenos */
    @media (max-width: 1232px) {
    .lk-emerald {
        --em-edge: 18px;           --em-navtop: 10px;         --em-brandtop: 19px;
        --em-ui: 14px;             --em-mark: 11px;           --em-markgap: 5px;
        --em-btnh: 34px;           --em-radius: 5px;          --em-navw: 96px;
        --em-navgap: 2px;          --em-authw: 108px;         --em-authgap: 7px;
        --em-btnx: 24px;           --em-heroy: -2px;          --em-herow: 910px;
        --em-herogap: 34px;        --em-h1: 44px;             --em-sub: 20px;
        --em-subw: 519px;          --em-subtop: 30px;         --em-ctagap: 7px;
        --em-barh: 47px;           --em-barpad: 0px;          --em-barrowgap: 0px;
        --em-logogap: 35px;        --em-logo: 17.3px;         --em-icongap: 7px;
        --em-rule: 48px;           --em-trust: 17px;          --em-trustgap: 7px;
        --em-btntext: 10px;
    }
    }

    /* x0.50 cromo · x0.59 hero — 1024 apaisado, el ultimo escalon con barra ancha */
    @media (max-width: 1040px) {
    .lk-emerald {
        --em-edge: 15px;           --em-navtop: 8px;          --em-brandtop: 16px;
        --em-ui: 12px;             --em-mark: 9px;            --em-markgap: 4px;
        --em-btnh: 28px;           --em-radius: 4px;          --em-navw: 80px;
        --em-navgap: 2px;          --em-authw: 90px;          --em-authgap: 6px;
        --em-btnx: 20px;           --em-heroy: -2px;          --em-herow: 763px;
        --em-herogap: 28px;        --em-h1: 37px;             --em-sub: 17px;
        --em-subw: 435px;          --em-subtop: 25px;         --em-ctagap: 6px;
        --em-barh: 40px;           --em-barpad: 0px;          --em-barrowgap: 0px;
        --em-logogap: 29px;        --em-logo: 14.5px;         --em-icongap: 6px;
        --em-rule: 40px;           --em-trust: 14px;          --em-trustgap: 6px;
        --em-btntext: 10px;
    }
    }

    /* ── Ventanas bajas ────────────────────────────────────────────────
     * En un portatil con la ventana poco alta manda el alto antes que el
     * ancho: el hero va centrado en la pagina y la barra de confianza esta
     * soldada abajo, y alrededor de `height < 700 x escala` las dos se
     * encuentran. Estos son los gemelos en altura de los escalones de ancho
     * y llevan las mismas tablas; la mitad `min-width` impide que un gemelo
     * pise a un escalon mas estrecho que ya eligio un juego menor. */

    /* x0.72 · x0.83 */
    @media (max-height: 660px) and (min-width: 1449px) {
    .lk-emerald {
        --em-edge: 22px;           --em-navtop: 12px;         --em-brandtop: 23px;
        --em-ui: 17px;             --em-mark: 13px;           --em-markgap: 6px;
        --em-btnh: 40px;           --em-radius: 6px;          --em-navw: 115px;
        --em-navgap: 3px;          --em-authw: 130px;         --em-authgap: 9px;
        --em-btnx: 29px;           --em-heroy: -3px;          --em-herow: 1071px;
        --em-herogap: 40px;        --em-h1: 52px;             --em-sub: 23px;
        --em-subw: 611px;          --em-subtop: 35px;         --em-ctagap: 9px;
        --em-barh: 57px;           --em-barpad: 0px;          --em-barrowgap: 0px;
        --em-logogap: 42px;        --em-logo: 20.8px;         --em-icongap: 9px;
        --em-rule: 58px;           --em-trust: 20px;          --em-trustgap: 9px;
        --em-btntext: 12px;
    }
    }

    /* x0.50 · x0.59 */
    @media (max-height: 500px) and (min-width: 1041px) {
    .lk-emerald {
        --em-edge: 15px;           --em-navtop: 8px;          --em-brandtop: 16px;
        --em-ui: 12px;             --em-mark: 9px;            --em-markgap: 4px;
        --em-btnh: 28px;           --em-radius: 4px;          --em-navw: 80px;
        --em-navgap: 2px;          --em-authw: 90px;          --em-authgap: 6px;
        --em-btnx: 20px;           --em-heroy: -2px;          --em-herow: 763px;
        --em-herogap: 28px;        --em-h1: 37px;             --em-sub: 17px;
        --em-subw: 435px;          --em-subtop: 25px;         --em-ctagap: 6px;
        --em-barh: 40px;           --em-barpad: 0px;          --em-barrowgap: 0px;
        --em-logogap: 29px;        --em-logo: 14.5px;         --em-icongap: 6px;
        --em-rule: 40px;           --em-trust: 14px;          --em-trustgap: 6px;
        --em-btntext: 10px;
    }
    }

    /* ══ RESPONSIVE ═════════════════════════════════════════════════════
     * Un movil no es un escritorio pequeno. Por debajo de 860px el marco
     * deja de escalarse y se reapila: la barra de cinco placas sale (no
     * sobrevive a ningun tamano que un movil pueda permitirse), el hero deja
     * de ir centrado en la pagina y toma la banda entera entre el nav y la
     * barra — de modo que el titular se mide contra la ventana y no contra
     * una columna de 1260, y sigue siendo lo mas alto de la pantalla — y la
     * barra de confianza se queda solo con los logotipos.
     *
     * La tabla sigue siendo la tabla — mismos nombres, sus propios pixeles.
     * Cinco escalones en vez de uno, porque el titular es la medida que hay
     * que recortar de nuevo en cada ventana del tamano de una mano. */
    @media (max-width: 860px) {
    .lk-emerald {
        --em-edge: 20px;           --em-navtop: 14px;         --em-brandtop: 25px;
        --em-ui: 15px;             --em-mark: 12px;           --em-markgap: 6px;
        --em-btnh: 38px;           --em-radius: 6px;          --em-navw: 0px;
        --em-navgap: 0px;          --em-authw: auto;          --em-authgap: 8px;
        --em-btnx: 18px;           --em-heroy: 0px;           --em-herow: auto;
        --em-herogap: 28px;        --em-h1: 39px;             --em-sub: 19px;
        --em-subw: auto;           --em-subtop: 24px;         --em-ctagap: 10px;
        --em-barh: 52px;           --em-barpad: 12px;         --em-barrowgap: 8px;
        --em-logogap: 24px;        --em-logo: 17px;           --em-icongap: 7px;
        --em-rule: 40px;           --em-trust: 13px;          --em-trustgap: 8px;
        --em-btntext: 11px;
    }

    /* La barra no cabe; el lockup y las dos acciones cargan con la parte
     * de arriba. */
    .lk-emerald .lkx-nav { display: none }

    /* Las acciones se dimensionan por su etiqueta, no por una placa de 180px. */
    .lk-emerald .lkx-navend .lk-pill { width: auto; padding: 0 var(--em-btnx) }

    /* El hero reclama la banda entre la fila del nav y la barra en lugar
     * del centro de la pagina, para que ninguna de las dos pueda chocar
     * con el, y abarca la medida completa en vez de una columna de 1260
     * reducida. */
    .lk-emerald .lkx-copy {
        left: var(--em-edge); right: var(--em-edge);
        top: calc(var(--em-navtop) + var(--em-btnh) + 24px);
        bottom: calc(var(--em-barh) + 24px);
        width: auto; max-width: none; transform: none;
    }

    .lk-emerald .lkx-sub { width: auto }
    .lk-emerald .lkx-cta { flex-wrap: wrap }

    /* La linea de confianza sale: a este ancho choca con la esquina del
     * marco, y los logotipos — el sentido de un logo wall — sostienen la
     * barra ellos solos, centrados. */
    }

    /* ── Movil grande, tablet en vertical ─────────────────────────── */
    @media (max-width: 760px) {
    .lk-emerald {
        --em-edge: 20px;           --em-navtop: 13px;         --em-brandtop: 23px;
        --em-ui: 14px;             --em-mark: 11px;           --em-markgap: 6px;
        --em-btnh: 36px;           --em-radius: 6px;          --em-navw: 0px;
        --em-navgap: 0px;          --em-authw: auto;          --em-authgap: 8px;
        --em-btnx: 16px;           --em-heroy: 0px;           --em-herow: auto;
        --em-herogap: 25px;        --em-h1: 34px;             --em-sub: 17px;
        --em-subw: auto;           --em-subtop: 21px;         --em-ctagap: 9px;
        --em-barh: 50px;           --em-barpad: 12px;         --em-barrowgap: 8px;
        --em-logogap: 22px;        --em-logo: 16px;           --em-icongap: 7px;
        --em-rule: 36px;           --em-trust: 12px;          --em-trustgap: 7px;
        --em-btntext: 10px;
    }
    }

    /* ── Movil ────────────────────────────────────────────────────── */
    @media (max-width: 640px) {
    .lk-emerald {
        --em-edge: 18px;           --em-navtop: 12px;         --em-brandtop: 21px;
        --em-ui: 13px;             --em-mark: 10px;           --em-markgap: 5px;
        --em-btnh: 34px;           --em-radius: 6px;          --em-navw: 0px;
        --em-navgap: 0px;          --em-authw: auto;          --em-authgap: 7px;
        --em-btnx: 15px;           --em-heroy: 0px;           --em-herow: auto;
        --em-herogap: 22px;        --em-h1: 29px;             --em-sub: 16px;
        --em-subw: auto;           --em-subtop: 18px;         --em-ctagap: 8px;
        --em-barh: 48px;           --em-barpad: 11px;         --em-barrowgap: 7px;
        --em-logogap: 18px;        --em-logo: 15px;           --em-icongap: 6px;
        --em-rule: 32px;           --em-trust: 12px;          --em-trustgap: 7px;
        --em-btntext: 10px;
    }
    }

    /* ── ...y en uno pequeno ──────────────────────────────────────── */
    @media (max-width: 520px) {
    .lk-emerald {
        --em-edge: 16px;           --em-navtop: 11px;         --em-brandtop: 19px;
        --em-ui: 12px;             --em-mark: 9px;            --em-markgap: 5px;
        --em-btnh: 32px;           --em-radius: 6px;          --em-navw: 0px;
        --em-navgap: 0px;          --em-authw: auto;          --em-authgap: 6px;
        --em-btnx: 13px;           --em-heroy: 0px;           --em-herow: auto;
        --em-herogap: 20px;        --em-h1: 23px;             --em-sub: 15px;
        --em-subw: auto;           --em-subtop: 16px;         --em-ctagap: 8px;
        --em-barh: 46px;           --em-barpad: 10px;         --em-barrowgap: 6px;
        --em-logogap: 14px;        --em-logo: 14px;           --em-icongap: 5px;
        --em-rule: 28px;           --em-trust: 11px;          --em-trustgap: 6px;
        --em-btntext: 10px;
    }
    }

    /* ── El hardware mas estrecho ─────────────────────────────────── */
    @media (max-width: 400px) {
    .lk-emerald {
        --em-edge: 14px;           --em-navtop: 10px;         --em-brandtop: 17px;
        --em-ui: 11px;             --em-mark: 8px;            --em-markgap: 4px;
        --em-btnh: 29px;           --em-radius: 5px;          --em-navw: 0px;
        --em-navgap: 0px;          --em-authw: auto;          --em-authgap: 5px;
        --em-btnx: 11px;           --em-heroy: 0px;           --em-herow: auto;
        --em-herogap: 18px;        --em-h1: 18px;             --em-sub: 14px;
        --em-subw: auto;           --em-subtop: 14px;         --em-ctagap: 7px;
        --em-barh: 42px;           --em-barpad: 10px;         --em-barrowgap: 6px;
        --em-logogap: 8px;         --em-logo: 11px;           --em-icongap: 4px;
        --em-rule: 24px;           --em-trust: 10px;          --em-trustgap: 5px;
        --em-btntext: 10px;
    }
    }

    @media (prefers-reduced-motion: reduce) {
    html.lk-js .lk-stage [data-rise] { opacity: 1 }
    }

    /* ── El nav original de la pagina ──────────────────────────────────
     * ADAPTACION: la composicion trae su propio cromo (lockup, barra de
     * pildoras, acciones), asi que el nav fijo de la landing se esconde
     * mientras el hero esta en pantalla y aparece al pasarlo. Sin esto
     * habria dos navegaciones apiladas en los primeros 100svh. */
    body.hero-visible nav#navbar { opacity: 0; pointer-events: none; transform: translateY(-100%) }
    nav#navbar { transition: opacity .45s ease, transform .45s cubic-bezier(.22,1,.36,1), background .3s ease }

    
    /* ══════════════════════════════════════════════════════════════════
     * LOGO WALL — el resto de la pagina
     * ══════════════════════════════════════════════════════════════════
     * El mismo lenguaje del hero llevado a las siete secciones de abajo:
     * fondo negro, General Sans en regular, titulares en caja alta, placas
     * de cristal definidas por un pelo de 1px, y la escala en pixeles duros
     * bajando por los mismos escalones.
     *
     * Lo unico que NO se replica es el objeto animado: el vortice se queda
     * en el hero. Debajo, el suelo es negro a secas — que es como lee esta
     * composicion cuando no hay asset detras.
     *
     * Este bloque va el ultimo a proposito: sobrescribe las reglas de la
     * landing original a igualdad de especificidad, por orden de aparicion. */

    :root {
    /* La calle y la medida son las del hero: la pagina se lee como una sola
     * reticula de arriba abajo. */
    --pg-gutter: 30px;      --pg-max: 1260px;
    --pg-ui: 24px;          --pg-btntext: 17px;     --pg-btnh: 56px;
    --pg-radius: 8px;       --pg-hair: 1px;
    /* --pg-h2 es la cifra grande (stats, precios), en General Sans.
     * --pg-title es el titular de seccion, en Michroma: mide 0.899em por
     * caracter en caja alta frente a los ~0.55 de General Sans, asi que a
     * igual medida de columna necesita ~2/3 del cuerpo. */
    --pg-h2: 56px;         --pg-title: 44px;          --pg-lead: 24px;        --pg-body: 18px;
    --pg-small: 15px;       --pg-micro: 13px;
    --pg-sec: 112px;        --pg-gap: 20px;

    --pg-ink: #fff;
    --pg-dim: rgba(255, 255, 255, .62);
    --pg-dim2: rgba(255, 255, 255, .38);
    /* Sobre negro, el cristal negro del hero seria invisible. Un 3.5% de
     * blanco lo levanta lo justo para que la placa exista; el pelo sigue
     * siendo lo que le dibuja el borde. */
    --pg-plate: rgba(255, 255, 255, .035);
    --pg-plate-hi: rgba(255, 255, 255, .065);
    --pg-edge: rgba(255, 255, 255, .16);
    --pg-edge-hi: rgba(255, 255, 255, .34);
    }

    @media (max-width: 1720px) { :root {
    --pg-gutter: 25px;      --pg-max: 1205px;
    --pg-ui: 20px;          --pg-btntext: 14px;     --pg-btnh: 47px;
    --pg-radius: 7px;
    --pg-h2: 50px;         --pg-title: 42px;          --pg-lead: 22px;        --pg-body: 17px;
    --pg-small: 14px;       --pg-micro: 12px;
    --pg-sec: 96px;         --pg-gap: 18px;
    } }
    @media (max-width: 1448px) { :root {
    --pg-gutter: 22px;      --pg-max: 1071px;
    --pg-ui: 17px;          --pg-btntext: 12px;     --pg-btnh: 40px;
    --pg-radius: 6px;
    --pg-h2: 44px;         --pg-title: 37px;          --pg-lead: 20px;        --pg-body: 16px;
    --pg-small: 13px;       --pg-micro: 11px;
    --pg-sec: 84px;         --pg-gap: 16px;
    } }
    @media (max-width: 1232px) { :root {
    --pg-gutter: 18px;      --pg-max: 910px;
    --pg-ui: 14px;          --pg-btntext: 10px;     --pg-btnh: 34px;
    --pg-radius: 5px;
    --pg-h2: 38px;         --pg-title: 31px;          --pg-lead: 18px;        --pg-body: 15px;
    --pg-small: 13px;       --pg-micro: 11px;
    --pg-sec: 72px;         --pg-gap: 14px;
    } }
    @media (max-width: 860px) { :root {
    --pg-gutter: 20px;      --pg-max: none;
    --pg-ui: 15px;          --pg-btntext: 12px;     --pg-btnh: 38px;
    --pg-radius: 6px;
    --pg-h2: 34px;         --pg-title: 28px;          --pg-lead: 18px;        --pg-body: 15px;
    --pg-small: 13px;       --pg-micro: 11px;
    --pg-sec: 64px;         --pg-gap: 14px;
    } }
    @media (max-width: 520px) { :root {
    --pg-gutter: 16px;
    --pg-h2: 28px;         --pg-title: 22px;          --pg-lead: 16px;        --pg-body: 14px;
    --pg-sec: 52px;
    } }

    /* ── El suelo ──────────────────────────────────────────────────────
     * Negro, y nada mas. Fuera la rejilla y la lluvia binaria: eran el
     * lenguaje anterior y aqui pelearian con las placas. */
    body {
    background: #000;
    color: var(--pg-ink);
    font-family: var(--font-general), system-ui, sans-serif;
    }
    .bg-grid, canvas#particles-canvas { display: none !important }
    ::selection { background: rgba(255, 255, 255, .18); color: #fff }
    ::-webkit-scrollbar { width: 10px }
    ::-webkit-scrollbar-track { background: #000 }
    ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, .18); border-radius: 0 }
    ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, .34) }

    /* ── Reticula ──────────────────────────────────────────────────── */
    section { padding-left: var(--pg-gutter); padding-right: var(--pg-gutter); padding-bottom: var(--pg-sec) }
    #caracteristicas { padding-top: var(--pg-sec) }
    .container { max-width: var(--pg-max); margin: 0 auto }

    /* ── El nav fijo, ya con el cromo del hero ─────────────────────── */
    nav#navbar {
    padding: 0;
    background: rgba(0, 0, 0, .72);
    -webkit-backdrop-filter: blur(var(--em-plate-blur, 12px));
    backdrop-filter: blur(var(--em-plate-blur, 12px));
    border-bottom: var(--pg-hair) solid var(--pg-edge);
    box-shadow: none;
    }
    nav#navbar.scrolled { background: rgba(0, 0, 0, .86); box-shadow: none }
    .nav-inner { max-width: none; padding: 0 var(--pg-gutter); height: calc(var(--pg-btnh) + 20px) }
    .logo-icon, .logo-wrap div { animation: none !important }
    .logo-wrap img { filter: drop-shadow(0 0 6px rgba(255, 255, 255, .18)) }
    /* El logotipo del nav venia clavado a 98px en linea y se salia de una
     * barra de 77. Lo atamos a la escala de la pagina; el !important es para
     * ganarle al atributo style del markup original. */
    .nav-inner .logo-wrap > div { height: auto !important }
    .nav-inner .logo-wrap img { height: calc(var(--pg-btnh) * .72) !important }
    .nav-links { gap: var(--pg-gap) }
    .nav-links a {
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-btntext); font-weight: 400; letter-spacing: normal;
    text-transform: none; color: rgba(255, 255, 255, .72);
    }
    .nav-links a::after { display: none }
    .nav-links a:hover { color: #fff; transform: none; text-decoration: underline; text-underline-offset: .25em }
    .btn-nav {
    font-family: var(--font-general), system-ui, sans-serif;
    display: inline-flex; align-items: center; justify-content: center;
    height: var(--pg-btnh); padding: 0 28px;
    background: #fff; color: #0a0a0a;
    border: 0; border-radius: var(--pg-radius);
    font-size: var(--pg-btntext); font-weight: 500; letter-spacing: normal;
    text-transform: none; box-shadow: none;
    }
    .btn-nav:hover { background: rgba(255, 255, 255, .88); transform: none; box-shadow: none }
    .mobile-toggle { color: #fff; font-size: var(--pg-ui) }

    /* ── Botones ───────────────────────────────────────────────────────
     * Dos recetas, como en el hero. Abajo el relleno solido se invierte a
     * blanco: sobre negro, un boton negro no existiria. */
    .btn-primary, .btn-ghost {
    font-family: var(--font-general), system-ui, sans-serif;
    display: inline-flex; align-items: center; justify-content: center; gap: 10px;
    height: var(--pg-btnh); padding: 0 32px;
    border-radius: var(--pg-radius);
    font-size: var(--pg-btntext); font-weight: 500; letter-spacing: normal;
    text-transform: none; text-decoration: none; white-space: nowrap;
    transition: background .25s ease, border-color .25s ease, color .25s ease;
    }
    .btn-primary { background: #fff; color: #0a0a0a; border: 0; box-shadow: none }
    .btn-primary:hover { background: rgba(255, 255, 255, .88); transform: none; box-shadow: none }
    .btn-ghost { background: transparent; color: #fff; border: var(--pg-hair) solid rgba(255, 255, 255, .5); box-shadow: none; backdrop-filter: none }
    .btn-ghost:hover { background: rgba(255, 255, 255, .07); border-color: #fff; transform: none; box-shadow: none }

    /* ── Cabeceras de seccion ──────────────────────────────────────────
     * La etiqueta hereda el gesto de la barra de confianza: caja alta,
     * pequena, y una regla de 2px que se desvanece al alejarse. */
    .section-head { margin-bottom: calc(var(--pg-sec) * .48) }
    .section-label {
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-micro); font-weight: 400;
    letter-spacing: .08em; text-transform: uppercase;
    color: var(--pg-dim); gap: 12px; margin-bottom: 20px;
    }
    .section-label::before {
    content: ''; width: 56px; height: 2px; flex: none;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, #fff 100%);
    }
    .section-head.center .section-label { justify-content: center }
    .section-label i { display: none }
    .section-title {
    font-family: var(--font-display);
    font-size: var(--pg-title); font-weight: 400; line-height: 1.1;
    letter-spacing: normal; text-transform: uppercase;
    background: none; -webkit-text-fill-color: currentColor; color: #fff;
    margin-bottom: 18px;
    }
    .section-sub {
    font-size: var(--pg-lead); font-weight: 400; line-height: 1.35;
    color: var(--pg-dim); max-width: 719px;
    }

    /* ── Placas ────────────────────────────────────────────────────────
     * Una sola receta para tarjetas, bots, planes, clientes, CTA y
     * formulario: fondo casi negro, pelo de 1px, 8px de radio. */
    .card, .bot-card, .plan-card, .cta-wrap, .contact-form {
    background: var(--pg-plate);
    border: 0;
    box-shadow: inset 0 0 0 var(--pg-hair) var(--pg-edge);
    border-radius: var(--pg-radius);
    -webkit-backdrop-filter: blur(12px);
    backdrop-filter: blur(12px);
    transition: background .25s ease, box-shadow .25s ease;
    }
    .card:hover, .bot-card:hover, .plan-card:hover {
    background: var(--pg-plate-hi);
    box-shadow: inset 0 0 0 var(--pg-hair) var(--pg-edge-hi);
    transform: none;
    border-color: transparent;
    }
    .card::before { display: none }
    .card, .bot-card { padding: 32px }
    .planes-grid { gap: var(--pg-gap) }
    /* Tres columnas fijas, no auto-fit: con auto-fit salian cuatro y las
     * seis tarjetas quedaban en 4+2, con un hueco al final de la segunda
     * fila. A tres, caracteristicas y bots caen en 3+3 y clientes en 3. */
    .spotlight-grid, .bots-grid { gap: var(--pg-gap); grid-template-columns: repeat(3, 1fr) }
    @media (max-width: 1040px) { .spotlight-grid, .bots-grid { grid-template-columns: repeat(2, 1fr) } }
    @media (max-width: 640px)  { .spotlight-grid, .bots-grid, .planes-grid { grid-template-columns: 1fr } }

    /* Los iconos dejan de ser fichas azules: cuadro con pelo y glifo blanco. */
    .card-icon, .bot-avatar {
    width: 48px; height: 48px; border-radius: var(--pg-radius);
    background: transparent; box-shadow: inset 0 0 0 var(--pg-hair) var(--pg-edge);
    color: #fff; font-size: var(--pg-small); margin-bottom: 24px;
    }
    .bot-avatar { margin-bottom: 0 }
    .card-title, .bot-name {
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-ui); font-weight: 400; letter-spacing: normal;
    color: #fff; margin-bottom: 12px;
    }
    .bot-name { margin-bottom: 4px }
    .card-body, .bot-desc { font-size: var(--pg-small); line-height: 1.55; color: var(--pg-dim) }
    .bot-role { font-size: var(--pg-micro); color: var(--pg-dim2) }
    .bot-tag {
    background: transparent; border: var(--pg-hair) solid var(--pg-edge);
    border-radius: var(--pg-radius); padding: 6px 12px;
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-micro); color: var(--pg-dim);
    }

    /* ── Stats ─────────────────────────────────────────────────────── */
    .stats-section {
    background: #000; box-shadow: none;
    border-top: var(--pg-hair) solid var(--pg-edge);
    border-bottom: var(--pg-hair) solid var(--pg-edge);
    -webkit-backdrop-filter: none; backdrop-filter: none;
    padding: calc(var(--pg-sec) * .38) var(--pg-gutter);
    }
    .stats-inner { max-width: var(--pg-max) }
    .stat-item:not(:last-child)::after { background: var(--pg-edge) }
    .stat-num {
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-h2); font-weight: 400; letter-spacing: normal;
    background: none; -webkit-text-fill-color: currentColor; color: #fff;
    }
    .stat-label {
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-micro); letter-spacing: .08em; text-transform: uppercase;
    color: var(--pg-dim); margin-top: 10px;
    }

    /* ── Planes ────────────────────────────────────────────────────── */
    .plan-card { padding: 36px }
    .plan-card.featured { box-shadow: inset 0 0 0 var(--pg-hair) var(--pg-edge-hi); border-color: transparent }
    .plan-popular {
    top: -13px; background: #fff; color: #0a0a0a;
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-micro); font-weight: 500; letter-spacing: .06em;
    padding: 6px 16px; border-radius: var(--pg-radius);
    }
    .plan-name {
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-micro); font-weight: 400; letter-spacing: .08em;
    text-transform: uppercase; color: var(--pg-dim); margin-bottom: 16px;
    }
    .plan-price {
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-h2); font-weight: 400; color: #fff; margin-bottom: 6px;
    }
    .plan-price sup { font-size: var(--pg-lead); color: var(--pg-dim); vertical-align: super }
    .plan-period { font-size: var(--pg-micro); color: var(--pg-dim2); margin-bottom: 28px }
    .plan-divider { border-top: var(--pg-hair) solid var(--pg-edge); margin-bottom: 28px }
    .plan-features { margin-bottom: 32px }
    .plan-features li {
    font-size: var(--pg-small); color: var(--pg-dim);
    padding: 11px 0; border-bottom: var(--pg-hair) solid rgba(255, 255, 255, .06);
    }
    .plan-features li i { color: #fff; font-size: var(--pg-micro) }
    .plan-card .btn-ghost, .plan-card .btn-primary { width: 100%; border-radius: var(--pg-radius) }

    /* ── Clientes ──────────────────────────────────────────────────── */
    .cli-top { display: flex; align-items: center; gap: 14px; margin-bottom: 20px }
    .cli-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    background: transparent; box-shadow: inset 0 0 0 var(--pg-hair) var(--pg-edge);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-micro); font-weight: 400; color: #fff; flex: none;
    }
    .cli-name { font-size: var(--pg-small); font-weight: 400; color: #fff }
    .cli-role { font-size: var(--pg-micro); color: var(--pg-dim2) }
    .cli-quote { font-size: var(--pg-small); line-height: 1.55; color: var(--pg-dim); font-style: normal }
    /* Las estrellas salen del dorado: esta composicion es monocroma. */
    .cli-stars { display: flex; gap: 3px; margin-top: 16px; color: rgba(255, 255, 255, .55); font-size: var(--pg-micro) }

    /* ── CTA ───────────────────────────────────────────────────────── */
    .cta-wrap { padding: calc(var(--pg-sec) * .62) var(--pg-gutter) }
    .cta-wrap .section-title { font-size: var(--pg-title) }
    .cta-lead {
    font-size: var(--pg-lead); line-height: 1.35; color: var(--pg-dim);
    max-width: 719px; margin: 0 auto 40px;
    }
    .cta-btns { display: flex; gap: var(--pg-gap); justify-content: center; flex-wrap: wrap }

    /* ── Formulario ────────────────────────────────────────────────── */
    .contact-form { max-width: 719px; padding: 40px }
    .form-group { margin-bottom: 24px }
    .form-row { gap: var(--pg-gap) }
    .form-group label {
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-micro); font-weight: 400; letter-spacing: .08em;
    text-transform: uppercase; color: var(--pg-dim); margin-bottom: 10px;
    }
    .form-group input, .form-group textarea, .form-group select {
    height: var(--pg-btnh); padding: 0 16px;
    background: transparent;
    border: var(--pg-hair) solid var(--pg-edge);
    border-radius: var(--pg-radius);
    color: #fff;
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-small);
    transition: border-color .25s ease;
    }
    .form-group textarea { height: auto; min-height: 140px; padding: 16px; resize: vertical; line-height: 1.5 }
    .form-group input::placeholder, .form-group textarea::placeholder { color: var(--pg-dim2) }
    .form-group select option { background: #0a0a0a; color: #fff }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: rgba(255, 255, 255, .55) }
    #submitBtn { width: 100%; border-radius: var(--pg-radius) }
    #formMsg { color: var(--pg-dim) !important; font-size: var(--pg-small) !important }

    /* ── Footer ────────────────────────────────────────────────────── */
    footer {
    background: #000; border-top: var(--pg-hair) solid var(--pg-edge);
    padding: var(--pg-sec) var(--pg-gutter) calc(var(--pg-sec) * .5);
    }
    .footer-inner { max-width: var(--pg-max); gap: 48px }
    .footer-brand p { font-size: var(--pg-small); line-height: 1.55; color: var(--pg-dim); margin-top: 20px }
    .footer-col h4 {
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-micro); font-weight: 400; letter-spacing: .08em;
    text-transform: uppercase; color: #fff; margin-bottom: 20px;
    }
    .footer-col ul li a { font-size: var(--pg-small); color: var(--pg-dim); padding: 7px 0 }
    .footer-col ul li a:hover { color: #fff; text-decoration: underline; text-underline-offset: .25em }
    .social-links a {
    width: 40px; height: 40px; border-radius: var(--pg-radius);
    background: transparent; box-shadow: inset 0 0 0 var(--pg-hair) var(--pg-edge);
    color: var(--pg-dim); font-size: var(--pg-small);
    display: inline-flex; align-items: center; justify-content: center;
    }
    .social-links a:hover { color: #fff; box-shadow: inset 0 0 0 var(--pg-hair) var(--pg-edge-hi); background: transparent; transform: none }
    .footer-bottom {
    max-width: var(--pg-max); margin-top: calc(var(--pg-sec) * .5);
    padding-top: 28px; border-top: var(--pg-hair) solid var(--pg-edge);
    font-size: var(--pg-micro); color: var(--pg-dim2);
    }
    
    /* ══════════════════════════════════════════════════════════════════
     * SPOTLIGHT FRAMES — puerto a JS plano del componente de Originkit
     * ══════════════════════════════════════════════════════════════════
     * Una fila de lamas verticales estrechas. Una se abre a lo ancho y las
     * demas se desplazan para hacerle sitio, asi que la tira se lee como un
     * acordeon horizontal y no como un grupo de tarjetas sueltas. La lama
     * abierta la marca un selector: una caja de un pelo sobre ella y dos
     * lineas guia que salen de sus bordes superior e inferior.
     *
     * Adaptaciones respecto al original, que era para fotos:
     *  - cada lama es una TARJETA (icono + titulo + cuerpo), no una imagen;
     *  - cerrada muestra el icono y el titulo en vertical, para que siga
     *    siendo identificable — el truco del original (ensenar la banda
     *    central de la foto) sobre texto daria un galimatias;
     *  - el contenido abierto se maqueta al ancho ABIERTO y se recorta, que
     *    es el mismo principio que aplica el original a las fotos: asi el
     *    texto no se recompone mientras la lama se abre;
     *  - cada lama lleva su imagen de fondo y solo la ensena al hover.
     *
     * La fuente es React + Framer Motion; aqui las transiciones vuelven a
     * ser CSS, que es como lo resolvia el proyecto del que parte. */

    #caracteristicas { position: relative; overflow: hidden }
    #caracteristicas > .container { position: relative; z-index: 1 }

    /* ── La tira ───────────────────────────────────────────────────── */
    .sf-strip { position: relative; width: 100% }
    .sf-track {
    position: relative; width: 100%; height: var(--sf-h, 420px);
    --sf-ease: cubic-bezier(.075, .82, .165, 1);
    --sf-dur: 1s;
    }

    .sf-slat {
    position: absolute; top: 0; height: 100%;
    overflow: hidden; cursor: pointer;
    border-radius: var(--pg-radius);
    background: var(--pg-plate);
    box-shadow: inset 0 0 0 1px var(--pg-edge);
    transition: left var(--sf-dur) var(--sf-ease),
                width var(--sf-dur) var(--sf-ease),
                background .5s ease, box-shadow .5s ease;
    will-change: left, width;
    outline: none;
    }
    /* Mientras se mide o se redimensiona no hay transicion: si no, la tira
     * viaja desde el origen en el primer pintado. */
    .sf-track.sf-noanim .sf-slat,
    .sf-track.sf-noanim .sf-selector { transition: none !important }

    .sf-slat.is-open { background: var(--pg-plate-hi); box-shadow: none }
    .sf-slat:focus-visible { box-shadow: inset 0 0 0 2px #fff }

    /* Atenuacion de las cerradas — el `dim` del original. */
    .sf-inner {
    position: absolute; inset: 0;
    filter: brightness(.62);
    transition: filter .6s var(--sf-ease), opacity .9s cubic-bezier(.16, 1, .3, 1),
                transform .9s cubic-bezier(.16, 1, .3, 1);
    }
    .sf-slat.is-open .sf-inner { filter: none }

    /* Entrada escalonada: 0.9s de duracion, 40ms entre lamas. El delay lo
     * pone el script por indice. */
    .sf-track.sf-enter .sf-inner { opacity: 0; transform: translateY(28px) }
    .sf-track.sf-enter.sf-in .sf-inner { opacity: 1; transform: none }

    /* ── Estado cerrado: icono + titulo en vertical ─────────────────── */
    .sf-shut {
    position: absolute; inset: 0;
    display: flex; flex-direction: column; align-items: center;
    /* El padding-top iguala al del estado abierto: asi el icono no da un
     * salto vertical al abrirse la lama. */
    padding: 36px 0 28px; gap: 24px;
    opacity: 1; transition: opacity .38s var(--sf-ease);
    }
    .sf-slat.is-open .sf-shut { opacity: 0; pointer-events: none }
    .sf-shut .card-icon { margin-bottom: 0; flex: none }
    .sf-vlabel {
    writing-mode: vertical-rl; transform: rotate(180deg);
    white-space: nowrap; overflow: hidden;
    font-size: var(--pg-small); font-weight: 400; letter-spacing: .02em;
    color: rgba(255, 255, 255, .78);
    }

    /* ── Estado abierto ────────────────────────────────────────────────
     * Maquetado al ancho abierto y anclado a la izquierda: la lama lo
     * recorta al abrirse en vez de recomponerlo. */
    .sf-open {
    position: absolute; top: 0; left: 0; height: 100%;
    width: var(--sf-open-w, 560px);
    padding: 36px; display: flex; flex-direction: column; align-items: flex-start;
    opacity: 0; transition: opacity .5s var(--sf-ease) .18s;
    pointer-events: none;
    }
    .sf-slat.is-open .sf-open { opacity: 1 }
    .sf-open .card-title { margin-top: 24px }
    .sf-open .card-body { max-width: 42ch }
    .sf-num {
    margin-top: auto;
    font-size: var(--pg-micro); letter-spacing: .08em;
    color: rgba(255, 255, 255, .38);
    }

    /* ── El selector ───────────────────────────────────────────────────
     * Caja sobre la lama abierta y dos guias saliendo de sus bordes. Van en
     * la misma curva que las lamas: es lo que hace que el conjunto se lea
     * como un solo gesto y no como tres cosas moviendose a la vez. */
    .sf-selector {
    position: absolute; top: 0; height: 100%;
    border: 1px solid #fff;
    border-radius: var(--pg-radius);
    pointer-events: none; z-index: 10;
    transition: left var(--sf-dur) var(--sf-ease), width var(--sf-dur) var(--sf-ease);
    will-change: left, width;
    }
    .sf-line { position: absolute; left: 50%; width: 1px; height: var(--sf-line, 96px) }
    /* Se desvanecen al alejarse de la caja, como la regla de la barra de
     * confianza — el mismo gesto grafico que el resto de la pagina. */
    .sf-line-t { bottom: 100%; transform: translateX(-50%);
    background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, #fff 100%) }
    .sf-line-b { top: 100%; transform: translateX(-50%);
    background: linear-gradient(180deg, #fff 0%, rgba(255, 255, 255, 0) 100%) }

    /* ── La imagen de cada lama ────────────────────────────────────────
     * Va dentro de .sf-inner, detras del contenido, y solo se ve mientras
     * el puntero esta encima: la lama abierta por defecto no la ensena
     * hasta que la tocas. En tactil no hay hover, asi que ahi la ensena la
     * lama abierta (la que se ha tocado). El velo va cargado a la izquierda,
     * que es donde cae el texto. */
    .sf-bg {
    position: absolute; inset: 0; z-index: 0;
    background-size: cover; background-position: center;
    opacity: 0; transition: opacity .55s var(--sf-ease);
    pointer-events: none;
    }
    .sf-bg::after {
    content: ''; position: absolute; inset: 0;
    background:
        linear-gradient(90deg, rgba(0, 0, 0, .86) 0%, rgba(0, 0, 0, .58) 55%, rgba(0, 0, 0, .3) 100%),
        linear-gradient(0deg, rgba(0, 0, 0, .55) 0%, rgba(0, 0, 0, 0) 45%);
    }
    .sf-shut, .sf-open { z-index: 1 }
    @media (hover: hover) and (pointer: fine) {
    .sf-slat.is-open:hover .sf-bg { opacity: 1 }
    }
    @media (hover: none), (pointer: coarse) {
    .sf-slat.is-open .sf-bg { opacity: 1 }
    }
    /* En columna (movil) la lama es estatica: hace falta un contexto para
     * que la imagen la llene, y el contenido por encima. */
    @media (max-width: 860px) {
    .sf-slat { position: relative }
    .sf-open { position: relative; z-index: 1 }
    }

    /* ── Escalones ─────────────────────────────────────────────────────
     * La tira se escala entera para caber en el marco, asi que aqui solo
     * bajan el alto y el aire de las guias. */
    @media (max-width: 1448px) { .sf-track { --sf-h: 380px } .sf-selector { --sf-line: 80px } }
    @media (max-width: 1232px) { .sf-track { --sf-h: 340px } .sf-selector { --sf-line: 64px } }

    /* Por debajo de 860 el acordeon no cabe: no hay ancho que repartir
     * entre seis lamas. Vuelve a ser una columna de tarjetas, con el fondo
     * cambiando igual al tocar cada una. */
    @media (max-width: 860px) {
    .sf-track { position: static; height: auto; display: grid; gap: var(--pg-gap) }
    .sf-slat {
        position: static; height: auto; width: auto !important; left: auto !important;
        transition: background .4s ease, box-shadow .4s ease;
    }
    .sf-inner { position: static; filter: none }
    .sf-shut { display: none }
    .sf-open {
        position: static; width: auto; height: auto; opacity: 1;
        padding: 28px; pointer-events: auto;
    }
    .sf-open .card-title { margin-top: 20px }
    /* Aqui no hay hueco sobrante que repartir, asi que el `margin-top:auto`
     * del contador no separa nada: se lo damos explicito. */
    .sf-open .sf-num { margin-top: 20px }
    .sf-selector { display: none }
    .sf-slat.is-open { box-shadow: inset 0 0 0 1px var(--pg-edge-hi) }
    }

    @media (prefers-reduced-motion: reduce) {
    .sf-slat, .sf-selector, .sf-inner, .sf-shut, .sf-open, .sf-bg { transition-duration: .01ms !important }
    }
    
    /* ══════════════════════════════════════════════════════════════════
     * GOOEY NAV — puerto a JS plano del componente de React Bits
     * ══════════════════════════════════════════════════════════════════
     * La pildora activa viaja al enlace pulsado y desde ella sale un
     * reventon de burbujas. El efecto "gooey" no es una imagen: son
     * particulas blancas desenfocadas a las que un filtro SVG les umbraliza
     * el alfa, de modo que los bordes difusos se vuelven duros y las que se
     * tocan se funden en una sola mancha.
     *
     * Adaptaciones: la lista cuelga directa del contenedor (anidar un
     * elemento nav dentro de nav#navbar seria HTML invalido), los tamanos
     * salen de los tokens de la pagina, el radio de la pildora es el de la
     * casa (--pg-radius) en vez del 10px del original, y la fusion se hace
     * con feColorMatrix en lugar del blur+contrast+mezcla del componente
     * (ver el porque junto a .effect.filter). */

    .gooey-nav-container { position: relative }

    /* Centrado en la barra, no en el hueco que dejan el logo y el CTA:
     * asi los enlaces caen en el centro real de la pantalla. */
    .nav-inner { position: relative }
    /* Centrado con flex sobre la barra entera, en vez de con un
     * translateX(-50%): el contenedor abarca todo el ancho y el puntero se
     * devuelve solo a la lista, asi que la caja del efecto se mide contra un
     * origen estable. */
    .nav-inner .gooey-nav-container {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none;
    }
    .nav-inner .gooey-nav-container ul { pointer-events: auto }

    .gooey-nav-container ul {
    display: flex; align-items: center; gap: var(--pg-gap);
    list-style: none; padding: 0; margin: 0;
    position: relative; z-index: 3;
    }

    .gooey-nav-container ul li {
    position: relative; cursor: pointer;
    border-radius: var(--pg-radius);
    color: rgba(255, 255, 255, .72);
    transition: color .3s ease, box-shadow .3s ease;
    box-shadow: 0 0 0 1.5px transparent;
    }

    .gooey-nav-container ul li a {
    display: inline-block;
    padding: .55em 1em;
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-btntext); font-weight: 400;
    letter-spacing: normal; text-transform: none;
    color: inherit; text-decoration: none;
    }
    /* El subrayado al hover sobraba en cuanto hay pildora. */
    .gooey-nav-container ul li a:hover { color: inherit; text-decoration: none; transform: none }
    .gooey-nav-container ul li:not(.active):hover { color: #fff }
    .gooey-nav-container ul li:focus-within:has(:focus-visible) { box-shadow: 0 0 0 1.5px #fff }

    .gooey-nav-container ul li::after {
    content: ''; position: absolute; inset: 0;
    border-radius: var(--pg-radius);
    background: #fff; opacity: 0; transform: scale(0);
    transition: all .3s ease; z-index: -1;
    }
    .gooey-nav-container ul li.active { color: #0a0a0a }
    .gooey-nav-container ul li.active::after { opacity: 1; transform: scale(1) }

    .gooey-nav-container .effect {
    position: absolute; left: 0; top: 0; width: 0; height: 0;
    opacity: 1; pointer-events: none;
    display: grid; place-items: center; z-index: 1;
    }
    .gooey-nav-container .effect.text {
    color: #fff; transition: color .3s ease;
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-btntext); font-weight: 400; white-space: nowrap;
    }
    .gooey-nav-container .effect.text.active { color: #0a0a0a }

    /* La fusion de burbujas la hace un filtro SVG, no el
     * `blur(7px) contrast(100)` + `mix-blend-mode: lighten` del original.
     *
     * Aquel truco necesita un campo NEGRO OPACO detras contra el que
     * recortar las manchas, y ese negro solo desaparece si el modo de mezcla
     * encuentra un respaldo con el que operar. La barra lleva
     * `backdrop-filter`, que crea un grupo aislado: fuera de ella el
     * respaldo es transparente, la mezcla no tiene nada que hacer y la placa
     * se pintaba opaca 100px por debajo del nav, tapando el principio de la
     * seccion.
     *
     * feColorMatrix umbraliza el canal ALFA, asi que funciona sobre
     * transparencia: mismo aspecto de manchas fundiendose, sin placa negra,
     * sin modo de mezcla y sin nada que recortar. */
    .gooey-nav-container .effect.filter { filter: url(#gooeyGoo) }
    .gooey-nav-container svg.gooey-defs { position: absolute; width: 0; height: 0 }
    .gooey-nav-container .effect.filter::after {
    content: ''; position: absolute; inset: 0;
    background: #fff; transform: scale(0); opacity: 0; z-index: -1;
    border-radius: var(--pg-radius);
    }
    .gooey-nav-container .effect.active::after { animation: gooey-pill .3s ease both }

    @keyframes gooey-pill { to { transform: scale(1); opacity: 1 } }

    /* Las burbujas. La pagina es monocroma, asi que los cuatro colores del
     * componente son blanco y blancos rotos. */
    .gooey-nav-container {
    --color-1: #ffffff; --color-2: #eef3fb;
    --color-3: #d8e4f6; --color-4: #ffffff;
    }

    .gooey-nav-container .particle,
    .gooey-nav-container .point {
    display: block; opacity: 0; width: 20px; height: 20px;
    border-radius: 100%; transform-origin: center;
    }
    .gooey-nav-container .particle {
    --time: 5s;
    position: absolute; top: calc(50% - 8px); left: calc(50% - 8px);
    animation: gooey-particle calc(var(--time)) ease 1 -350ms;
    }
    .gooey-nav-container .point {
    background: var(--color); opacity: 1;
    animation: gooey-point calc(var(--time)) ease 1 -350ms;
    }

    @keyframes gooey-particle {
    0%   { transform: rotate(0deg) translate(calc(var(--start-x)), calc(var(--start-y)));
            opacity: 1; animation-timing-function: cubic-bezier(.55, 0, 1, .45) }
    70%  { transform: rotate(calc(var(--rotate) * .5)) translate(calc(var(--end-x) * 1.2), calc(var(--end-y) * 1.2));
            opacity: 1; animation-timing-function: ease }
    85%  { transform: rotate(calc(var(--rotate) * .66)) translate(calc(var(--end-x)), calc(var(--end-y)));
            opacity: 1 }
    100% { transform: rotate(calc(var(--rotate) * 1.2)) translate(calc(var(--end-x) * .5), calc(var(--end-y) * .5));
            opacity: 1 }
    }
    @keyframes gooey-point {
    0%   { transform: scale(0); opacity: 0; animation-timing-function: cubic-bezier(.55, 0, 1, .45) }
    25%  { transform: scale(calc(var(--scale) * .25)) }
    38%  { opacity: 1 }
    65%  { transform: scale(var(--scale)); opacity: 1; animation-timing-function: ease }
    85%  { transform: scale(var(--scale)); opacity: 1 }
    100% { transform: scale(0); opacity: 0 }
    }

    /* En movil el menu es el desplegable del boton hamburguesa: ni pildora
     * ni burbujas, y el contenedor deja de posicionar nada. */
    @media (max-width: 860px) {
    .nav-inner .gooey-nav-container { position: static; display: block; pointer-events: auto }
    .gooey-nav-container .effect { display: none }
    .gooey-nav-container ul li::after { display: none }
    .gooey-nav-container ul li.active { color: #fff }
    /* `.gooey-nav-container ul` le gana en especificidad a `.nav-links`, asi
     * que hay que repetir aqui el ocultar/desplegar del menu hamburguesa o
     * los enlaces se quedan visibles junto al boton. */
    .gooey-nav-container ul { display: none }
    .gooey-nav-container ul.open { display: flex; flex-direction: column; align-items: stretch }
    .nav-links.open {
        top: calc(var(--pg-btnh) + 20px);
        background: rgba(0, 0, 0, .96);
        border-bottom: 1px solid var(--pg-edge);
    }
    }

    @media (prefers-reduced-motion: reduce) {
    .gooey-nav-container .effect.filter { display: none }
    }
    

    /* ══════════════════════════════════════════════════════════════════
     * BOUNCE CARDS — puerto a JS plano del componente de React Bits
     * ══════════════════════════════════════════════════════════════════
     * El abanico del original, tal cual: las seis tarjetas van en absoluto,
     * superpuestas y giradas, y al pasar el puntero la tocada endereza su
     * inclinacion mientras las demas se apartan a los lados hasta dejarla
     * entera a la vista. Entran con el rebote elastico escalonado.
     *
     * Del CSS del componente se conserva la mecanica (.card en absoluto,
     * apiladas, giradas por estilo en linea); lo que NO se copia es su
     * aspecto — 200px cuadrados, borde blanco de 5px, radio 25 y sombra —
     * porque era el marco de una foto y estas tarjetas ya tienen el suyo del
     * sistema de la pagina.
     *
     * Medidas: ocho tarjetas de 320, centros cada 134, o sea 186 de solape.
     * El empujon del hover es 190, justo lo que hace falta para deshacerlo. */

    .bots-grid {
    position: relative;
    display: block;
    width: 100%;
    height: var(--bc-h, 430px);
    /* La tira se escala entera para caber en el marco, como el original
     * hacia con su containerWidth fijo. */
    transform: scale(var(--bc-scale, 1));
    transform-origin: center center;
    }

    .bc-card {
    position: absolute;
    left: 50%; top: 50%;
    width: var(--bc-w, 320px);
    height: var(--bc-card-h, 340px);
    margin-left: calc(var(--bc-w, 320px) / -2);
    margin-top: calc(var(--bc-card-h, 340px) / -2);
    padding: 24px;
    overflow: hidden;
    transform-origin: center;
    will-change: transform;
    /* La animacion la lleva GSAP por estilo en linea; una transicion CSS
     * sobre transform pelearia con ella fotograma a fotograma. */
    }

    /* Cada tarjeta tapa a la anterior, asi que necesita fondo opaco: con el
     * 3.5% translucido del sistema se transparentarian unas sobre otras. */
    .bc-card { background: #0b0b0b }
    .bc-card:hover { background: #101010 }

    /* La tocada sube al frente. Mientras las vecinas se apartan hay 0.4s de
     * solape, y sin esto la de al lado le pasaria por encima. */
    .bc-card.bc-hot { z-index: 10; background: #121212 }

    .bc-card .bot-desc { margin-bottom: 14px }
    .bc-card .bot-tags { position: absolute; left: 24px; bottom: 24px }

    /* ── Por debajo de 1040 no hay ancho para un abanico de seis ────────
     * Vuelve a ser una columna de tarjetas completas. El script detecta el
     * cambio y desactiva el hover, que ahi no tendria a donde empujar. */
    @media (max-width: 1040px) {
    .bots-grid {
        display: grid; grid-template-columns: repeat(2, 1fr);
        gap: var(--pg-gap); height: auto; transform: none;
    }
    .bc-card {
        position: static; width: auto; height: auto;
        margin: 0; padding: 28px; transform: none !important;
    }
    .bc-card .bot-tags { position: static }
    .bc-card, .bc-card:hover, .bc-card.bc-hot { background: var(--pg-plate); z-index: auto }
    .bc-card:hover { background: var(--pg-plate-hi) }
    }
    @media (max-width: 640px) {
    .bots-grid { grid-template-columns: 1fr }
    }
    
    /* ══════════════════════════════════════════════════════════════════
     * STACK — puerto a JS plano del componente de React Bits
     * ══════════════════════════════════════════════════════════════════
     * Un mazo de tarjetas: la de arriba recta, las de detras giradas y algo
     * mas pequenas, asomando. Se arrastra para mandarla al fondo, se pulsa
     * para lo mismo, y sola pasa de tarjeta cada 10 segundos.
     *
     * Las dos capas de transformacion son las del original y hacen cosas
     * distintas, por eso no se pueden fusionar:
     *  - .st-rotate lleva el arrastre — desplazamiento y volteo 3D en X/Y;
     *  - .st-card lleva la posicion en el mazo — giro en Z y escala, con el
     *    origen en 90% 90%, que es lo que hace que el mazo se abra desde una
     *    esquina en vez de desde el centro.
     *
     * Adaptaciones al pasar de fotos a tarjetas: el mazo tiene medida propia
     * (una foto se estira, un testimonio no), las tarjetas llevan fondo
     * opaco porque se tapan entre si, y el contenido se centra en vertical
     * en lugar del centrado a ambos ejes que el original usaba para encajar
     * una imagen. */

    /* El giro de las de detras es aleatorio (±5° sobre la base), asi que su
     * vuelo horizontal varia en cada barajada y no hay holgura fija que lo
     * cubra siempre. `clip` recorta lo que se salga sin crear contenedor de
     * scroll — `hidden` en un solo eje forzaria `auto` en el otro. */
    #clientes { overflow-x: clip }

    .st-stack {
    position: relative;
    width: var(--st-w, 520px);
    height: var(--st-h, 300px);
    max-width: 100%;
    margin: 0 auto;
    perspective: 600px;
    }

    .st-rotate {
    position: absolute; inset: 0;
    cursor: grab;
    will-change: transform;
    touch-action: none;   /* el arrastre lo gestionamos nosotros */
    }
    .st-rotate.is-drag { cursor: grabbing }

    .st-card {
    position: absolute; inset: 0;
    display: flex; flex-direction: column; justify-content: center;
    border-radius: var(--pg-radius);
    overflow: hidden;
    /* El del componente. Es lo que hace que el mazo se abra desde la esquina
     * inferior derecha en vez de desde el centro: con 50% 50% las de detras
     * asomarian por los dos lados y se perderia el gesto de baraja. */
    transform-origin: 90% 90%;
    /* Opaco: las de detras asoman y con el 3.5% translucido del sistema se
     * transparentarian unas sobre otras. */
    background: #0b0b0b;
    box-shadow: inset 0 0 0 1px var(--pg-edge), 0 24px 60px rgba(0, 0, 0, .6);
    will-change: transform;
    user-select: none;
    }
    .st-card * { user-select: none }

    /* La de arriba se despega un poco del resto. */
    .st-rotate:last-child .st-card,
    .st-card.is-top { box-shadow: inset 0 0 0 1px var(--pg-edge-hi), 0 28px 70px rgba(0, 0, 0, .7) }

    /* Pista de que hay mas de una: cuantas quedan y cual toca. */
    .st-dots {
    display: flex; justify-content: center; gap: 8px;
    margin-top: 28px;
    }
    .st-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: rgba(255, 255, 255, .22);
    transition: background .4s ease, width .4s ease;
    }
    .st-dot.is-on { background: #fff; width: 20px; border-radius: 3px }

    @media (max-width: 1232px) { .st-stack { --st-w: 470px } }
    @media (max-width: 860px)  { .st-stack { --st-w: 440px; --st-h: 320px } }
    /* Holgura para el giro: con el origen en 90% 90% las de detras se salen
     * del contenedor, y a ancho completo eso desbordaba la pagina 23px. */
    @media (max-width: 520px)  { .st-stack { --st-w: calc(100% - 48px); --st-h: 340px } }

    @media (prefers-reduced-motion: reduce) {
    .st-rotate, .st-card { transition: none }
    }
    
    /* ══════════════════════════════════════════════════════════════════
     * FOLDER — puerto a JS plano del componente de React Bits
     * ══════════════════════════════════════════════════════════════════
     * Una carpeta que se abre al pulsarla y despliega tres papeles. Al pasar
     * el puntero por encima levanta la solapa y los papeles asoman; ya
     * abierta, cada papel sigue al raton con un iman suave.
     *
     * Adaptaciones al pasar de papeles decorativos a los planes:
     *  - la carpeta no mide 100x80 sino lo que haga falta para que un plan
     *    quepa dentro de un papel. Todo sale de --fld-w/--fld-h, asi que las
     *    proporciones del original (papeles al 70/80/90% de ancho y
     *    80/70/60% de alto) se mantienen a cualquier tamano;
     *  - ABIERTA los tres papeles pasan a medir lo mismo: son tres planes al
     *    mismo nivel, no un monton de hojas sueltas. El componente ya cambia
     *    la altura de los papeles al abrir, asi que esto va con su idea;
     *  - los papeles van oscuros y la carpeta blanca, como se pidio. En el
     *    original los papeles eran blancos porque la carpeta era de color;
     *  - y se separan mucho mas que en el original — ver .folder.open. */

    /* Altura fija con la carpeta apoyada abajo: el hueco de arriba es
     * justo el que van a ocupar los papeles al desplegarse. Reservarlo
     * siempre evita que al abrir la seccion pegue un salto y empuje al
     * resto de la pagina hacia abajo. */
    .fld-wrap {
    display: flex; flex-direction: column;
    justify-content: flex-end; align-items: center;
    min-height: 620px;
    }
    .fld-wrap > .folder { align-self: center }

    .folder {
    --fld-w: 360px;
    --fld-h: 280px;
    /* Cuanto suben los papeles: los laterales poco, el destacado mucho mas
     * para que quede claro cual es. */
    --fld-rise: 90px;
    --fld-rise-mid: 200px;
    /* Papel abierto: la medida de un plan. */
    --fld-paper-w: 360px;
    --fld-paper-h: 440px;
    /* Cuanto se apartan del centro los dos laterales. */
    --fld-spread: 410px;

    position: relative;
    transition: all .2s ease-in;
    cursor: pointer;
    }

    .folder:hover { transform: translateY(-8px) }
    .folder:hover .paper { transform: translate(-50%, 0%) }
    .folder:hover .folder__front { transform: skew(15deg) scaleY(.6) }
    .folder:hover .right { transform: skew(-15deg) scaleY(.6) }

    .folder__back {
    position: relative;
    width: var(--fld-w);
    height: var(--fld-h);
    background: var(--folder-back-color);
    border-radius: 0 10px 10px 10px;
    }
    /* La pestana de la carpeta, proporcional como en el original (30x10
     * sobre 100x80). */
    .folder__back::after {
    position: absolute; z-index: 0; bottom: 98%; left: 0; content: '';
    width: calc(var(--fld-w) * .3);
    height: calc(var(--fld-h) * .125);
    background: var(--folder-back-color);
    border-radius: 8px 8px 0 0;
    }

    .paper {
    position: absolute; z-index: 2;
    bottom: 10%; left: 50%;
    transform: translate(-50%, 10%);
    width: 70%; height: 80%;
    background: var(--paper-1);
    border-radius: 10px;
    transition: all .35s ease-in-out;
    overflow: hidden;
    }
    .paper:nth-child(2) { background: var(--paper-2); width: 80%; height: 70% }
    .paper:nth-child(3) { background: var(--paper-3); width: 90%; height: 60% }

    .folder__front {
    position: absolute; z-index: 3;
    width: 100%; height: 100%;
    background: var(--folder-color);
    border-radius: 5px 10px 10px 10px;
    transform-origin: bottom;
    transition: all .35s ease-in-out;
    }
    /* Logo ESTELAR (versión negra) estampado en la cara visible de la
     * carpeta cerrada. Solo en .right porque es la que queda encima
     * cuando ambos frentes están planos (mismo tamaño, superpuestos);
     * al abrirse, esta mitad se lleva el logo consigo. */
    .folder__front.right {
    background-image: url('img/logo_final_black.png');
    background-repeat: no-repeat;
    background-position: center;
    background-size: 42%;
    }

    .folder:focus-visible { outline: 2px solid #fff; outline-offset: 4px; border-radius: 10px }

    /* ── Abierta ───────────────────────────────────────────────────────
     * El original deja los papeles muy encimados —translate(-120%/10%/-50%)
     * sobre su propio ancho— porque son hojas sueltas y da igual leerlas.
     * Aqui cada papel es un plan, asi que se separan por --fld-spread hasta
     * quedar uno al lado del otro, y el giro baja de 15° a 6° para que el
     * texto no salga torcido. */
    .folder.open { transform: translateY(-8px) }

    .folder.open .paper {
    width: var(--fld-paper-w);
    height: var(--fld-paper-h);
    /* Anclados al centro de la carpeta, no a su base: asi el recorrido
     * hacia arriba es el que dicen --fld-rise y no depende del alto del
     * papel, que cambia al abrir. */
    top: 50%; bottom: auto;
    }
    .folder.open .paper:nth-child(1) {
    transform: translate(calc(-50% - var(--fld-spread)), calc(-50% - var(--fld-rise))) rotateZ(-4deg);
    }
    .folder.open .paper:nth-child(2) {
    transform: translate(calc(-50% + var(--fld-spread)), calc(-50% - var(--fld-rise))) rotateZ(4deg);
    }
    .folder.open .paper:nth-child(3) {
    transform: translate(-50%, calc(-50% - var(--fld-rise-mid))) rotateZ(0deg);
    z-index: 4;   /* el destacado, al frente */
    }

    /* El iman del original: el papel sigue al puntero. */
    .folder.open .paper:hover {
    transform: translate(
        calc(-50% - var(--fld-spread) + var(--magnet-x, 0px)),
        calc(-50% - var(--fld-rise) + var(--magnet-y, 0px))) rotateZ(-4deg) scale(1.04);
    z-index: 5;
    }
    .folder.open .paper:nth-child(2):hover {
    transform: translate(
        calc(-50% + var(--fld-spread) + var(--magnet-x, 0px)),
        calc(-50% - var(--fld-rise) + var(--magnet-y, 0px))) rotateZ(4deg) scale(1.04);
    }
    .folder.open .paper:nth-child(3):hover {
    transform: translate(
        calc(-50% + var(--magnet-x, 0px)),
        calc(-50% - var(--fld-rise-mid) + var(--magnet-y, 0px))) rotateZ(0deg) scale(1.04);
    }

    .folder.open .folder__front { transform: skew(15deg) scaleY(.6) }
    .folder.open .right { transform: skew(-15deg) scaleY(.6) }

    /* ── El plan dentro del papel ──────────────────────────────────── */
    .paper .plan-card {
    position: absolute; inset: 0;
    width: auto; height: auto;
    padding: 26px 24px;
    border-radius: 10px;
    background: transparent; box-shadow: none;
    overflow: hidden;
    }
    .paper .plan-price { font-size: 40px; margin-bottom: 4px }
    .paper .plan-price sup { font-size: 18px }
    .paper .plan-period { margin-bottom: 16px }
    .paper .plan-divider { margin-bottom: 14px }
    .paper .plan-features { margin-bottom: 18px }
    .paper .plan-features li { padding: 7px 0 }
    .paper .plan-popular { top: 10px; left: auto; right: 14px; transform: none }
    .paper .btn-ghost, .paper .btn-primary { width: 100%; height: 44px }

    /* Un aviso de que hay que pulsar; se retira al abrir. */
    .fld-hint {
    display: block; width: 100%; text-align: center;
    margin-top: 24px;
    font-size: var(--pg-micro); letter-spacing: .08em; text-transform: uppercase;
    color: var(--pg-dim); transition: opacity .3s ease;
    }
    .fld-wrap.is-open .fld-hint { opacity: 0 }

    /* ── Sin sitio para el despliegue ──────────────────────────────────
     * Por debajo de 1240 los tres papeles ya no caben uno al lado del otro.
     * La carpeta se retira y los planes vuelven a su retisula, que es la
     * unica forma de que sigan siendo legibles. */
    @media (max-width: 1280px) {
    .fld-wrap { display: block; padding-top: 0 }
    .fld-hint { display: none }
    .folder, .folder__back { all: unset }
    .folder__back {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--pg-gap);
        align-items: start;
    }
    /* La pestana (::after) sigue con `position:absolute` -esa regla vive
     * fuera de este media query, para la carpeta 3D de escritorio- pero
     * `all:unset` le quita `position:relative` a .folder__back, su ancla.
     * Sin ancla, "absolute" sube hasta el bloque contenedor inicial y
     * "bottom:98%" la deja flotando cerca del top de toda la pagina. Ya
     * no hay carpeta cerrada que decorar aqui -es una rejilla de planes-
     * asi que se oculta en vez de darle una ancla nueva. */
    .folder__back::after { display: none }
    .folder__front { display: none }
    .paper, .folder.open .paper {
        position: static; width: auto !important; height: auto !important;
        transform: none !important; background: none; overflow: visible;
    }
    .paper .plan-card {
        position: static; padding: 36px;
        background: var(--pg-plate); box-shadow: inset 0 0 0 1px var(--pg-edge);
        border-radius: var(--pg-radius);
    }
    .paper .plan-price { font-size: var(--pg-h2) }
    .paper .plan-popular { top: -13px; left: 50%; right: auto; transform: translateX(-50%) }
    }
    @media (max-width: 860px) {
    .folder__back { grid-template-columns: 1fr }
    }
    
    /* ══════════════════════════════════════════════════════════════════
     * PROFILE CARD — puerto a JS plano del componente de React Bits
     * ══════════════════════════════════════════════════════════════════
     * La tarjeta se inclina en 3D siguiendo al puntero y por encima corre un
     * holograma: bandas de arcoiris fundidas en `color-dodge` sobre un
     * destello radial en `overlay`, mas un resplandor desenfocado por detras
     * que persigue al raton. Todo eso son degradados y modos de mezcla; el
     * script solo publica seis variables CSS con la posicion del puntero.
     *
     * Los degradados del holograma (--sunpillar-*, .pc-shine y sus dos
     * pseudos, .pc-glare) van VERBATIM: son el efecto. La tarjeta original
     * ya se dibuja sobre negro, asi que el arcoiris cae aqui como un tornasol
     * y no como un parche de color.
     *
     * Lo que hubo que rehacer es la geometria, que da por supuesto un
     * retrato vertical:
     *  - `height: 80svh; aspect-ratio: 0.718` -> ancho completo y alto por
     *    contenido, que esto es un banner;
     *  - la perspectiva sube de 500px a 1600: a 500px una tarjeta de 1260 de
     *    ancho se deforma como un gran angular;
     *  - `.pc-card *` afectaba a TODOS los descendientes (display:grid,
     *    pointer-events:none). En el original los hijos son capas a sangre;
     *    aqui dentro hay titular y botones, asi que la regla se acota a las
     *    capas y el contenido recupera el puntero. */

    .pc-card-wrapper {
    --card-radius: var(--pg-radius);
    /* El componente enmascara el holograma con una textura (su demo carga
     * un iconpattern.png). Sin ella no hay mascara y el arcoiris cubre la
     * tarjeta entera a plena potencia: se comia el titular. Va embebida
     * como SVG para no anadir una peticion mas. */
    --icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Crect width='60' height='60' fill='%23000'/%3E%3Ccircle cx='15' cy='15' r='6' fill='%23fff'/%3E%3Ccircle cx='45' cy='45' r='6' fill='%23fff'/%3E%3Crect x='38' y='8' width='14' height='14' rx='4' fill='%23999'/%3E%3Crect x='8' y='38' width='14' height='14' rx='4' fill='%23999'/%3E%3C/svg%3E");
    --grain: none;
    --inner-gradient: linear-gradient(145deg, rgba(255,255,255,.05) 0%, rgba(255,255,255,.02) 100%);
    --behind-glow-color: rgba(190, 215, 255, .55);
    --behind-glow-size: 55%;

    --pointer-x: 50%; --pointer-y: 50%;
    --pointer-from-center: 0; --pointer-from-top: .5; --pointer-from-left: .5;
    --card-opacity: 0;
    --rotate-x: 0deg; --rotate-y: 0deg;
    --background-x: 50%; --background-y: 50%;

    --sunpillar-1: hsl(2, 100%, 73%);   --sunpillar-2: hsl(53, 100%, 69%);
    --sunpillar-3: hsl(93, 100%, 69%);  --sunpillar-4: hsl(176, 100%, 76%);
    --sunpillar-5: hsl(228, 100%, 74%); --sunpillar-6: hsl(283, 100%, 73%);
    --sunpillar-clr-1: var(--sunpillar-1); --sunpillar-clr-2: var(--sunpillar-2);
    --sunpillar-clr-3: var(--sunpillar-3); --sunpillar-clr-4: var(--sunpillar-4);
    --sunpillar-clr-5: var(--sunpillar-5); --sunpillar-clr-6: var(--sunpillar-6);

    /* 500px en el original, para una tarjeta de ~390 de ancho. */
    perspective: 1600px;
    transform: translate3d(0, 0, .1px);
    position: relative;
    touch-action: none;
    }

    .pc-behind {
    position: absolute; inset: 0; z-index: 0; pointer-events: none;
    background: radial-gradient(circle at var(--pointer-x) var(--pointer-y),
        var(--behind-glow-color) 0%, transparent var(--behind-glow-size));
    filter: blur(50px) saturate(1.1);
    opacity: calc(.8 * var(--card-opacity));
    transition: opacity 200ms ease;
    }
    .pc-card-wrapper:hover, .pc-card-wrapper.active { --card-opacity: 1 }

    .pc-card-shell { position: relative; z-index: 1 }

    .pc-card {
    display: grid;
    width: 100%;
    /* La tarjeta es un elemento section, asi que le caia el `section{padding:6rem
     * 2rem}` de la landing: 96px arriba y abajo, 25 a los lados. Su fondo
     * asomaba por ese marco y se veia un recuadro oscuro detras. */
    padding: 0;
    border-radius: var(--card-radius);
    position: relative;
    background: rgba(0, 0, 0, .9);
    box-shadow: rgba(0, 0, 0, .8) calc((var(--pointer-from-left) * 10px) - 3px)
        calc((var(--pointer-from-top) * 20px) - 6px) 20px -5px;
    transition: transform 1s ease;
    transform: translateZ(0) rotateX(0deg) rotateY(0deg);
    backface-visibility: hidden;
    overflow: hidden;
    }
    .pc-card:hover, .pc-card.active {
    transition: none;
    transform: translateZ(0) rotateX(var(--rotate-y)) rotateY(var(--rotate-x));
    }
    .pc-card-shell.entering .pc-card { transition: transform 180ms ease-out }

    /* En el original esto era `.pc-card *`. Acotado a las capas: si no,
     * ponia display:grid y pointer-events:none al titular y a los botones. */
    .pc-card > .pc-inside,
    .pc-inside > .pc-shine,
    .pc-inside > .pc-glare,
    .pc-inside > .pc-content {
    display: grid; grid-area: 1/-1;
    border-radius: var(--card-radius);
    }
    .pc-inside > .pc-shine,
    .pc-inside > .pc-glare { pointer-events: none }

    /* En el original esto era `position: absolute; inset: 0`, y no pasaba
     * nada porque la tarjeta tenia alto fijo (80svh). Aqui el alto lo pone
     * el contenido, asi que si la capa sale del flujo la tarjeta se queda en
     * 192px. En flujo, y con las tres capas en la misma celda de la
     * retisula, manda la mas alta — que es el contenido. */
    .pc-inside {
    position: relative;
    background-image: var(--inner-gradient);
    background-color: rgba(0, 0, 0, .9);
    transform: none;
    }

    /* ── El holograma, verbatim ─────────────────────────────────────── */
    .pc-shine {
    mask-image: var(--icon); mask-mode: luminance; mask-repeat: repeat; mask-size: 120px;
    mask-position: top calc(200% - (var(--background-y) * 5)) left calc(100% - var(--background-x));
    transition: filter .8s ease;
    filter: brightness(.66) contrast(1.33) saturate(.33) opacity(.42);
    animation: pc-holo-bg 18s linear infinite;
    mix-blend-mode: color-dodge;
    }
    .pc-shine, .pc-shine::after {
    --space: 5%; --angle: -45deg;
    transform: translate3d(0, 0, 1px);
    overflow: hidden; z-index: 3;
    background: transparent; background-size: cover; background-position: center;
    background-image:
        repeating-linear-gradient(0deg,
        var(--sunpillar-clr-1) calc(var(--space) * 1), var(--sunpillar-clr-2) calc(var(--space) * 2),
        var(--sunpillar-clr-3) calc(var(--space) * 3), var(--sunpillar-clr-4) calc(var(--space) * 4),
        var(--sunpillar-clr-5) calc(var(--space) * 5), var(--sunpillar-clr-6) calc(var(--space) * 6),
        var(--sunpillar-clr-1) calc(var(--space) * 7)),
        repeating-linear-gradient(var(--angle),
        #0e152e 0%, hsl(180, 10%, 60%) 3.8%, hsl(180, 29%, 66%) 4.5%,
        hsl(180, 10%, 60%) 5.2%, #0e152e 10%, #0e152e 12%),
        radial-gradient(farthest-corner circle at var(--pointer-x) var(--pointer-y),
        hsla(0, 0%, 0%, .1) 12%, hsla(0, 0%, 0%, .15) 20%, hsla(0, 0%, 0%, .25) 120%);
    background-position: 0 var(--background-y), var(--background-x) var(--background-y), center;
    background-blend-mode: color, hard-light;
    background-size: 500% 500%, 300% 300%, 200% 200%;
    background-repeat: repeat;
    }
    .pc-shine::before, .pc-shine::after {
    content: ''; background-position: center; background-size: cover;
    grid-area: 1/1; opacity: 0; transition: opacity .8s ease;
    }
    .pc-card:hover .pc-shine, .pc-card.active .pc-shine {
    filter: brightness(.8) contrast(1.4) saturate(.6) opacity(.72);
    animation-play-state: paused;
    }
    .pc-card:hover .pc-shine::before, .pc-card.active .pc-shine::before { opacity: .7 }
    .pc-card:hover .pc-shine::after, .pc-card.active .pc-shine::after { opacity: .7 }

    .pc-shine::before {
    background-image:
        linear-gradient(45deg, var(--sunpillar-4), var(--sunpillar-5), var(--sunpillar-6),
        var(--sunpillar-1), var(--sunpillar-2), var(--sunpillar-3)),
        radial-gradient(circle at var(--pointer-x) var(--pointer-y),
        hsl(0, 0%, 70%) 0%, hsla(0, 0%, 30%, .2) 90%),
        var(--grain);
    background-size: 250% 250%, 100% 100%, 220px 220px;
    background-position: var(--pointer-x) var(--pointer-y), center,
        calc(var(--pointer-x) * .01) calc(var(--pointer-y) * .01);
    background-blend-mode: color-dodge;
    filter: brightness(calc(2 - var(--pointer-from-center))) contrast(calc(var(--pointer-from-center) + 2))
        saturate(calc(.5 + var(--pointer-from-center)));
    mix-blend-mode: luminosity;
    }
    .pc-shine::after {
    background-position: 0 var(--background-y),
        calc(var(--background-x) * .4) calc(var(--background-y) * .5), center;
    background-size: 200% 300%, 700% 700%, 100% 100%;
    mix-blend-mode: difference;
    filter: brightness(.8) contrast(1.5);
    }

    .pc-glare {
    transform: translate3d(0, 0, 1.1px);
    overflow: hidden;
    background-image: radial-gradient(farthest-corner circle at var(--pointer-x) var(--pointer-y),
        hsl(248, 25%, 80%) 12%, hsla(207, 40%, 30%, .8) 90%);
    mix-blend-mode: overlay;
    filter: brightness(.8) contrast(1.2);
    z-index: 4;
    }

    @keyframes pc-holo-bg {
    0%   { background-position: 0 var(--background-y), 0 0, center }
    100% { background-position: 0 var(--background-y), 90% 90%, center }
    }

    /* ── El contenido ───────────────────────────────────────────────────
     * Mantiene el contra-paralaje del original (se mueve unos pixeles al
     * lado contrario del puntero), pero SIN su `mix-blend-mode: luminosity`:
     * alli servia para que el nombre recogiera el tornasol, y aqui dejaba el
     * titular y los botones lavados sobre el holograma. Un CTA tiene que
     * leerse. */
    .pc-content {
    position: relative; z-index: 5;
    transform: translate3d(
        calc(var(--pointer-from-left) * -6px + 3px),
        calc(var(--pointer-from-top) * -6px + 3px), .1px);
    }
    .pc-content .cta-wrap {
    background: none; box-shadow: none; border: 0; backdrop-filter: none;
    }
    .pc-content a, .pc-content button { pointer-events: auto }

    @media (prefers-reduced-motion: reduce) {
    .pc-card, .pc-card:hover, .pc-card.active { transform: none !important; transition: none }
    .pc-shine { animation: none }
    }
    
    /* ══════════════════════════════════════════════════════════════════
     * TARGET CURSOR — puerto a JS plano del componente de React Bits
     * ══════════════════════════════════════════════════════════════════
     * Un punto con cuatro escuadras que gira despacio y, al pasar sobre algo
     * marcado como objetivo, deja de girar y las escuadras se abren hasta
     * encuadrar el elemento. El `mix-blend-mode: difference` es lo que hace
     * que se vea siempre, sobre negro o sobre el vortice.
     *
     * El CSS va verbatim salvo el color, que sale de una variable para poder
     * cambiarlo desde el script como hace el prop cursorColor. */

    .target-cursor-wrapper {
    position: fixed; top: 0; left: 0;
    width: 0; height: 0;
    pointer-events: none;
    z-index: 2147483647;
    mix-blend-mode: difference;
    transform: translate(-50%, -50%);
    }

    .target-cursor-dot {
    position: absolute; left: 50%; top: 50%;
    width: 4px; height: 4px;
    background: var(--tc-color, #fff);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    will-change: transform;
    }

    .target-cursor-corner {
    position: absolute; left: 50%; top: 50%;
    width: 12px; height: 12px;
    border: 3px solid var(--tc-color, #fff);
    will-change: transform;
    }

    .corner-tl { transform: translate(-150%, -150%); border-right: none; border-bottom: none }
    .corner-tr { transform: translate(50%, -150%);   border-left: none;  border-bottom: none }
    .corner-br { transform: translate(50%, 50%);     border-left: none;  border-top: none }
    .corner-bl { transform: translate(-150%, 50%);   border-right: none; border-top: none }

    /* ── Ocultar el cursor del sistema ──────────────────────────────────
     * El componente solo pone `cursor: none` en el <body>, y con eso basta
     * en su demo. Esta pagina declara `cursor: pointer` en las pildoras,
     * `pointer` en las lamas y `grab` en el mazo de clientes, y esas reglas
     * le ganan al body: saldrian los dos cursores a la vez. La clase la pone
     * el script, y solo cuando hay raton de verdad. */
    html.tc-on, html.tc-on * { cursor: none !important }

    /* Un objetivo no necesita mas pista visual que las escuadras. */
    .cursor-target { cursor: none }
    
    /* ══════════════════════════════════════════════════════════════════
     * DRIFT WALL — puerto a JS plano del componente de React Bits
     * ══════════════════════════════════════════════════════════════════
     * Un muro de teselas en perspectiva que deriva sin fin: columnas que
     * suben y bajan alternadas a velocidades distintas, el plano entero
     * inclinado y siguiendo al puntero con un paralaje amortiguado, y la
     * tesela bajo el raton que se levanta hacia el espectador y frena su
     * columna. Los bordes se disuelven con una mascara.
     *
     * CSS verbatim. Lo unico que cambia es el color del velo: #060010 en el
     * original (su demo es violeta), negro aqui. Y `grayscale` activado,
     * que es un prop del componente: los logos van desaturados en reposo y
     * recuperan el color al pasar por encima — casa con la pagina. */

    .drift-wall {
    position: relative;
    width: 100%;
    height: var(--dw-h, 360px);
    overflow: hidden;
    perspective: var(--dw-perspective, 1200px);
    perspective-origin: 50% 50%;
    --dw-tile-w: 200px; --dw-tile-h: 132px; --dw-gap: 18px; --dw-radius: 14px;
    --dw-lift: 64px; --dw-dim: .55; --dw-gray: 0; --dw-overlay: #060010; --dw-edge: 40%;
    -webkit-mask-image:
        radial-gradient(ellipse 78% 82% at 50% 46%, #000 var(--dw-edge), transparent 100%),
        linear-gradient(to top, #000 var(--dw-edge), transparent 100%);
    -webkit-mask-composite: source-in;
    mask-image:
        radial-gradient(ellipse 78% 82% at 50% 46%, #000 var(--dw-edge), transparent 100%),
        linear-gradient(to top, #000 var(--dw-edge), transparent 100%);
    mask-composite: intersect;
    /* Aire entre el muro y el mazo de testimonios. */
    margin-bottom: 48px;
    }

    .drift-wall__plane {
    position: absolute; top: 50%; left: 50%;
    display: flex; flex-direction: row;
    transform-style: preserve-3d;
    cursor: pointer;
    transform-origin: 50% 50%;
    will-change: transform;
    }

    .drift-wall__col {
    position: relative;
    width: calc(var(--dw-tile-w) + var(--dw-gap));
    transform-style: preserve-3d;
    }

    .drift-wall__track {
    display: flex; flex-direction: column;
    will-change: transform;
    transform-style: preserve-3d;
    }

    .drift-wall__tile {
    position: relative; display: block;
    width: 100%; height: calc(var(--dw-tile-h) + var(--dw-gap));
    flex: 0 0 auto; outline: none;
    transform-style: preserve-3d;
    }

    /* ADAPTACION: en el original cada tesela es una foto que llena una
     * caja oscura (`cover` sobre #0b0b12). Aqui son logos con alfa y no
     * tiene que verse ninguna caja: la tesela es transparente, el logo se
     * ajusta dentro con `contain` y un poco de aire, y ni el velo ni la
     * sombra del hover dibujan el rectangulo — la sombra va por
     * drop-shadow, que sigue la silueta. */
    .drift-wall__inner {
    position: absolute; inset: calc(var(--dw-gap) / 2);
    display: flex; align-items: center; justify-content: center;
    border-radius: var(--dw-radius);
    overflow: visible;
    background: transparent;
    padding: 10px;
    opacity: var(--dw-dim);
    transform: translateZ(0);
    pointer-events: none;
    transition: transform .42s cubic-bezier(.22, 1, .36, 1),
                opacity .42s cubic-bezier(.22, 1, .36, 1),
                box-shadow .42s cubic-bezier(.22, 1, .36, 1);
    }

    .drift-wall__tile img {
    width: 100%; height: 100%; object-fit: contain; display: block;
    filter: grayscale(var(--dw-gray)) saturate(.92);
    transition: filter .42s cubic-bezier(.22, 1, .36, 1);
    user-select: none; -webkit-user-drag: none;
    }

    .drift-wall__overlay { display: none }   /* tintaba el rectangulo entero */

    .drift-wall__tile.is-active .drift-wall__inner,
    .drift-wall__tile:focus-visible .drift-wall__inner {
    opacity: 1;
    transform: translateZ(var(--dw-lift));
    }
    .drift-wall__tile.is-active img,
    .drift-wall__tile:focus-visible img {
    filter: grayscale(0) saturate(1.05) drop-shadow(0 18px 28px rgba(0, 0, 0, .75));
    }
    .drift-wall__tile.is-active .drift-wall__overlay,
    .drift-wall__tile:focus-visible .drift-wall__overlay { opacity: 0 }
    .drift-wall__tile:focus-visible .drift-wall__inner { outline: 2px solid rgba(255, 255, 255, .9); outline-offset: -2px }

    @media (max-width: 860px) { .drift-wall { --dw-h: 280px } }
    @media (prefers-reduced-motion: reduce) {
    .drift-wall__plane, .drift-wall__track { will-change: auto }
    }
    
    /* ── Selector de moneda (Planes) ─────────────────────────────────
     * Tres pildoras del sistema, la activa en solido. Los precios llevan
     * las tres monedas en data-*; el script solo cambia simbolo y cifra. */
    .cur-switch {
    display: inline-flex; gap: 6px; margin-top: 28px;
    padding: 4px; border-radius: var(--pg-radius);
    background: var(--pg-plate); box-shadow: inset 0 0 0 1px var(--pg-edge);
    }
    .cur-switch button {
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-micro); font-weight: 500; letter-spacing: .02em;
    height: 34px; padding: 0 16px;
    border: 0; border-radius: calc(var(--pg-radius) - 2px);
    background: transparent; color: var(--pg-dim);
    cursor: pointer; transition: background .25s ease, color .25s ease;
    }
    .cur-switch button:hover { color: #fff }
    .cur-switch button[aria-checked="true"] { background: #fff; color: #0a0a0a }
    .cur-switch button:focus-visible { outline: 2px solid #fff; outline-offset: 2px }
    .plan-price .plan-amt, .plan-price .plan-cur { transition: opacity .18s ease }
    .plan-price.is-swapping .plan-amt, .plan-price.is-swapping .plan-cur { opacity: 0 }
    /* Los pesos son cifras largas: se aprietan un poco para caber en el papel. */
    .plan-price[data-cur="cop"] .plan-amt { font-size: .82em; letter-spacing: -.01em }

    /* ── Aviso en la barra de datos ─────────────────────────────────── */
    .stats-notice {
    display: flex; align-items: center; justify-content: center; gap: 12px;
    max-width: var(--pg-max); margin: 0 auto 28px;
    padding-bottom: 22px;
    border-bottom: var(--pg-hair) solid var(--pg-edge);
    font-family: var(--font-general), system-ui, sans-serif;
    font-size: var(--pg-small); font-weight: 500; letter-spacing: .06em;
    text-transform: uppercase; color: #fff; text-align: center;
    }
    .stats-notice-dot {
    width: 8px; height: 8px; border-radius: 50%; flex: none;
    background: #fff;
    animation: stats-pulse 2.2s ease-out infinite;
    }
    @keyframes stats-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(255, 255, 255, .55) }
    100% { box-shadow: 0 0 0 12px rgba(255, 255, 255, 0) }
    }
    @media (prefers-reduced-motion: reduce) { .stats-notice-dot { animation: none } }
    </style>

    @viteReactRefresh
    @vite(['resources/css/gls-panel-utilities.css', 'resources/js/go-left-panel-mount.jsx'])
    </head>
    <body>
    <div class="main-container"><nav id="navbar">
    <div class="nav-inner">
        <a href="#inicio" class="logo-wrap">
        <div style="height:70px;display:flex;align-items:center"><img src="img/logo_final.png" alt="ESTELAR" height="78" style="height:98px;width:auto;display:block;object-fit:contain"></div>
        </a>
        <div class="gooey-nav-container">
        <ul class="nav-links" id="navLinks">
        <li><a href="#inicio">Inicio</a></li>
        <li><a href="#caracteristicas">Caracter&iacute;sticas</a></li>
        <li><a href="#clientes">Clientes</a></li>
        <li><a href="#planes">Planes</a></li>
        <li><a href="#contacto">Contacto</a></li>
        </ul>
        <span class="effect filter" aria-hidden="true"></span>
        <span class="effect text" aria-hidden="true"></span>
        <!-- El filtro que funde las burbujas. La region es holgada a
             proposito: las particulas viajan 90px y con la region por
             defecto (120% de la caja) se recortarian a media trayectoria. -->
        <svg class="gooey-defs" aria-hidden="true" focusable="false">
            <defs>
            <filter id="gooeyGoo" x="-300%" y="-500%" width="700%" height="1100%">
                <feGaussianBlur in="SourceGraphic" stdDeviation="7" result="blur"/>
                <feColorMatrix in="blur" mode="matrix"
                values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo"/>
                <feBlend in="SourceGraphic" in2="goo"/>
            </filter>
            </defs>
        </svg>
        </div>
        <a href="#contacto" class="btn-nav">Demo Gratuita</a>
        <button class="mobile-toggle" id="mobileToggle"><i class="fas fa-bars"></i></button>
    </div>
    </nav>

    <!-- ══ HERO · Vortex + Logo wall ═══════════════════════════════════
         La escena Three.js es el suelo vivo de la composicion; el marco de
         Figma flota encima. Nada aqui pinta un fondo propio.            -->
    <section class="lk-stage" id="inicio">

    <!-- El asset va aqui, detras de la composicion: primero en el DOM
         para que se quede debajo de todo lo demas. -->
    <canvas id="scene"></canvas>

    <div class="uip-layer lk lk-emerald">

        <!-- Figma "Frame 12" -->
        <a href="#inicio" class="lk-brand" data-rise="0">
        <img class="lk-mark" src="img/logo_final.png" alt="" aria-hidden="true">ESTELAR
        </a>

        <!-- Figma "Frame 40" -->
        <nav class="lkx-nav">
        <span class="lkx-links" data-rise="0">
            <a href="#inicio">Inicio</a>
            <a href="#caracteristicas">Caracter&iacute;sticas</a>
            <a href="#clientes">Clientes</a>
            <a href="#planes">Planes</a>
            <a href="#contacto">Contacto</a>
        </span>
        </nav>

        <!-- Figma "Frame 42" -->
        <span class="lkx-navend" data-rise="0">
        <a href="{{ route('login') }}" class="lk-pill lk-pill-ghost">Iniciar sesi&oacute;n</a>
        <a href="#contacto" class="lk-pill lk-pill-dark">Demo gratuita</a>
        </span>

        <!-- Figma "Frame 44" -->
        <div class="lkx-copy">
        <div class="lkx-group">
            <h1 class="lkx-h1" data-rise="1">
            <span>Automatiza tu empresa.</span>
            <span class="lkx-dim">Escala sin l&iacute;mites.</span>
            </h1>
            <p class="lkx-sub" data-rise="2">
            CRM de leads y ERPs que centralizan tus ventas, clientes y
            operaciones. Reduce el trabajo manual hasta un 80% desde una
            sola plataforma.
            </p>
        </div>
        <div class="lkx-cta" data-rise="3">
            <a href="#contacto" class="lk-pill lk-pill-dark">Demo gratuita</a>
            <a href="#caracteristicas" class="lk-pill lk-pill-ghost">Ver soluciones</a>
        </div>
        </div>



    </div>
    </section>


    <!-- STATS -->
    <div class="stats-section">
    <div class="stats-notice" role="status">
        <span class="stats-notice-dot" aria-hidden="true"></span>
        &iexcl;&iexcl;Abrimos una nueva sede en Colombia!!
    </div>
    <div class="stats-inner">
        <div class="stat-item"><span class="stat-num" data-count="850" data-suffix="+">0</span><div class="stat-label">Empresas Activas</div></div>
        <div class="stat-item"><span class="stat-num" data-count="99" data-suffix=".9%">0</span><div class="stat-label">Uptime Garantizado</div></div>
        <div class="stat-item"><span class="stat-num" data-count="3200" data-suffix="+">0</span><div class="stat-label">Procesos Automatizados</div></div>
        <div class="stat-item"><span class="stat-num" data-count="50" data-suffix="+">0</span><div class="stat-label">Integraciones Nativas</div></div>
    </div>
    </div><!-- CARACTERÍSTICAS -->
    <section id="caracteristicas">
    <div class="container">
        <div class="section-head center reveal">
        <div class="section-label"><i class="fas fa-microchip"></i> Capacidades</div>
        <h2 class="section-title">Todo lo que tu empresa necesita</h2>
        <p class="section-sub">Una plataforma unificada que elimina el caos operativo y pone la inteligencia artificial al servicio de tu negocio.</p>
        </div>
        <!-- Spotlight Frames: acordeon horizontal. Cada lama es una
             tarjeta y al abrirse cambia el fondo de la seccion. -->
        <div class="sf-strip" id="sfStrip">
        <div class="sf-track" role="tablist" aria-label="Capacidades">
            <div class="sf-slat" role="tab" tabindex="0" data-i="0"
                 aria-label="IA Conversacional" aria-selected="true">
            <div class="sf-inner">
                <div class="sf-bg" style="background-image:url(img/IA-Conversacional.jpg)" aria-hidden="true"></div>
                <div class="sf-shut" aria-hidden="true">
                <div class="card-icon"><i class="fas fa-brain"></i></div>
                <div class="sf-vlabel">IA Conversacional</div>
                </div>
                <div class="sf-open">
                <div class="card-icon"><i class="fas fa-brain"></i></div>
                <div class="card-title">IA Conversacional</div>
                <div class="card-body">Bots que entienden lenguaje natural, aprenden de cada interacci&oacute;n y resuelven consultas en segundos sin intervenci&oacute;n humana.</div>
                <div class="sf-num">01 / 06</div>
                </div>
            </div>
            </div>
            <div class="sf-slat" role="tab" tabindex="0" data-i="1"
                 aria-label="+50 Integraciones" aria-selected="false">
            <div class="sf-inner">
                <div class="sf-bg" style="background-image:url(img/Integraciones.jpg)" aria-hidden="true"></div>
                <div class="sf-shut" aria-hidden="true">
                <div class="card-icon"><i class="fas fa-plug"></i></div>
                <div class="sf-vlabel">+50 Integraciones</div>
                </div>
                <div class="sf-open">
                <div class="card-icon"><i class="fas fa-plug"></i></div>
                <div class="card-title">+50 Integraciones</div>
                <div class="card-body">Conecta con WhatsApp, Slack, Salesforce, SAP, Google Workspace y decenas de herramientas m&aacute;s con un solo clic.</div>
                <div class="sf-num">02 / 06</div>
                </div>
            </div>
            </div>
            <div class="sf-slat" role="tab" tabindex="0" data-i="2"
                 aria-label="Seguridad Enterprise" aria-selected="false">
            <div class="sf-inner">
                <div class="sf-bg" style="background-image:url(img/Seguridad.jpg)" aria-hidden="true"></div>
                <div class="sf-shut" aria-hidden="true">
                <div class="card-icon"><i class="fas fa-shield-halved"></i></div>
                <div class="sf-vlabel">Seguridad Enterprise</div>
                </div>
                <div class="sf-open">
                <div class="card-icon"><i class="fas fa-shield-halved"></i></div>
                <div class="card-title">Seguridad Enterprise</div>
                <div class="card-body">Cifrado AES-256, autenticaci&oacute;n multifactor, auditor&iacute;a completa y cumplimiento de ISO 27001 y GDPR.</div>
                <div class="sf-num">03 / 06</div>
                </div>
            </div>
            </div>
            <div class="sf-slat" role="tab" tabindex="0" data-i="3"
                 aria-label="Analytics en Tiempo Real" aria-selected="false">
            <div class="sf-inner">
                <div class="sf-bg" style="background-image:url(img/Analytics.jpg)" aria-hidden="true"></div>
                <div class="sf-shut" aria-hidden="true">
                <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                <div class="sf-vlabel">Analytics en Tiempo Real</div>
                </div>
                <div class="sf-open">
                <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                <div class="card-title">Analytics en Tiempo Real</div>
                <div class="card-body">Dashboards personalizables con m&eacute;tricas actualizadas al instante. Toma decisiones con datos, no con intuici&oacute;n.</div>
                <div class="sf-num">04 / 06</div>
                </div>
            </div>
            </div>
            <div class="sf-slat" role="tab" tabindex="0" data-i="4"
                 aria-label="Workflows sin Cdigo" aria-selected="false">
            <div class="sf-inner">
                <div class="sf-bg" style="background-image:url(img/Workflows.jpg)" aria-hidden="true"></div>
                <div class="sf-shut" aria-hidden="true">
                <div class="card-icon"><i class="fas fa-code-branch"></i></div>
                <div class="sf-vlabel">Workflows sin C&oacute;digo</div>
                </div>
                <div class="sf-open">
                <div class="card-icon"><i class="fas fa-code-branch"></i></div>
                <div class="card-title">Workflows sin C&oacute;digo</div>
                <div class="card-body">Editor visual drag-and-drop para dise&ntilde;ar flujos de trabajo complejos sin escribir una sola l&iacute;nea de c&oacute;digo.</div>
                <div class="sf-num">05 / 06</div>
                </div>
            </div>
            </div>
            <div class="sf-slat" role="tab" tabindex="0" data-i="5"
                 aria-label="Soporte 24 / 7" aria-selected="false">
            <div class="sf-inner">
                <div class="sf-bg" style="background-image:url(img/Soporte.jpg)" aria-hidden="true"></div>
                <div class="sf-shut" aria-hidden="true">
                <div class="card-icon"><i class="fas fa-headset"></i></div>
                <div class="sf-vlabel">Soporte 24 / 7</div>
                </div>
                <div class="sf-open">
                <div class="card-icon"><i class="fas fa-headset"></i></div>
                <div class="card-title">Soporte 24 / 7</div>
                <div class="card-body">Equipo t&eacute;cnico especializado disponible a cualquier hora. Tiempo de respuesta menor a 3 minutos para incidencias cr&iacute;ticas.</div>
                <div class="sf-num">06 / 06</div>
                </div>
            </div>
            </div>
            <div class="sf-selector" aria-hidden="true">
            <i class="sf-line sf-line-t"></i><i class="sf-line sf-line-b"></i>
            </div>
        </div>
        </div>
    </div>
    </section>

    <!-- BOTS & IA -->
    <section id="bots" style="padding-top:0">
    <div class="container">
        <div class="section-head center reveal">
        <div class="section-label"><i class="fas fa-robot"></i> Bots &amp; Agentes IA</div>
        <h2 class="section-title">Agentes listos para trabajar</h2>
        <p class="section-sub">Cada agente y cada ERP est&aacute; especializado en su sector. Desp&iacute;egalos en minutos y mide resultados desde el primer d&iacute;a.</p>
        </div>
        <div class="bots-grid">
        <div class="bot-card bc-card bc-card-0" data-rest="rotate(10deg) translate(-469px)" style="transform:rotate(10deg) translate(-469px)">
            <div class="bot-top"><div class="bot-avatar"><i class="fas fa-comments"></i></div><div><div class="bot-name">SupportBot Pro</div><div class="bot-role">Atenci&oacute;n al cliente &middot; 24/7</div></div></div>
            <p class="bot-desc">Resuelve consultas frecuentes, gestiona tickets y escala casos complejos al equipo humano de forma inteligente.</p>
            <div class="bot-tags"><span class="bot-tag">94% satisfacci&oacute;n</span><span class="bot-tag">&lt;2s respuesta</span></div>
        </div>
        <div class="bot-card bc-card bc-card-1" data-rest="rotate(-7deg) translate(-335px)" style="transform:rotate(-7deg) translate(-335px)">
            <div class="bot-top"><div class="bot-avatar"><i class="fas fa-chart-bar"></i></div><div><div class="bot-name">DataAnalyst AI</div><div class="bot-role">An&aacute;lisis de datos &middot; BI</div></div></div>
            <p class="bot-desc">Procesa miles de filas de datos, genera reportes autom&aacute;ticos y env&iacute;a alertas cuando detecta anomal&iacute;as.</p>
            <div class="bot-tags"><span class="bot-tag">10x m&aacute;s r&aacute;pido</span><span class="bot-tag">99% precisi&oacute;n</span></div>
        </div>
        <div class="bot-card bc-card bc-card-2" data-rest="rotate(5deg) translate(-201px)" style="transform:rotate(5deg) translate(-201px)">
            <div class="bot-top"><div class="bot-avatar"><i class="fas fa-envelope"></i></div><div><div class="bot-name">EmailFlow Bot</div><div class="bot-role">Email marketing &middot; CRM</div></div></div>
            <p class="bot-desc">Segmenta audiencias, crea campa&ntilde;as personalizadas y automatiza secuencias de seguimiento post-venta.</p>
            <div class="bot-tags"><span class="bot-tag">+35% apertura</span><span class="bot-tag">+22% conversi&oacute;n</span></div>
        </div>
        <div class="bot-card bc-card bc-card-3" data-rest="rotate(-3deg) translate(-67px)" style="transform:rotate(-3deg) translate(-67px)">
            <div class="bot-top"><div class="bot-avatar"><i class="fas fa-boxes-stacked"></i></div><div><div class="bot-name">InventoryAI</div><div class="bot-role">Inventario &middot; Supply chain</div></div></div>
            <p class="bot-desc">Predice demanda, genera &oacute;rdenes de compra autom&aacute;ticas y evita quiebres de stock con modelos predictivos.</p>
            <div class="bot-tags"><span class="bot-tag">-40% sobrestock</span><span class="bot-tag">98% exactitud</span></div>
        </div>
        <div class="bot-card bc-card bc-card-4" data-rest="rotate(3deg) translate(67px)" style="transform:rotate(3deg) translate(67px)">
            <div class="bot-top"><div class="bot-avatar"><i class="fas fa-file-invoice-dollar"></i></div><div><div class="bot-name">BillingBot</div><div class="bot-role">Facturaci&oacute;n &middot; Cobranzas</div></div></div>
            <p class="bot-desc">Genera facturas electr&oacute;nicas, env&iacute;a recordatorios de pago y reporta morosidad en tiempo real.</p>
            <div class="bot-tags"><span class="bot-tag">-60% morosidad</span><span class="bot-tag">SUNAT auto</span></div>
        </div>
        <div class="bot-card bc-card bc-card-5" data-rest="rotate(-5deg) translate(201px)" style="transform:rotate(-5deg) translate(201px)">
            <div class="bot-top"><div class="bot-avatar"><i class="fas fa-user-tie"></i></div><div><div class="bot-name">HRAssistant</div><div class="bot-role">RRHH &middot; Onboarding</div></div></div>
            <p class="bot-desc">Filtra CVs, programa entrevistas, gestiona onboarding digital y responde consultas laborales al instante.</p>
            <div class="bot-tags"><span class="bot-tag">5h ahorro/semana</span><span class="bot-tag">100% digital</span></div>
        </div>
        <div class="bot-card bc-card bc-card-6" data-rest="rotate(7deg) translate(335px)" style="transform:rotate(7deg) translate(335px)">
            <div class="bot-top"><div class="bot-avatar"><i class="fas fa-building"></i></div><div><div class="bot-name">ERP Inmobiliario</div><div class="bot-role">Inmobiliarias &middot; Ventas y alquileres</div></div></div>
            <p class="bot-desc">Centraliza propiedades, clientes, contratos y cobros. Seguimiento de cada lead desde la visita hasta la firma, con la cartera siempre al d&iacute;a.</p>
            <div class="bot-tags"><span class="bot-tag">Cartera unificada</span><span class="bot-tag">Contratos digitales</span></div>
        </div>
        <div class="bot-card bc-card bc-card-7" data-rest="rotate(-10deg) translate(469px)" style="transform:rotate(-10deg) translate(469px)">
            <div class="bot-top"><div class="bot-avatar"><i class="fas fa-graduation-cap"></i></div><div><div class="bot-name">ERP Colegios</div><div class="bot-role">Educaci&oacute;n &middot; Acad&eacute;mico y administrativo</div></div></div>
            <p class="bot-desc">Matr&iacute;culas, pensiones, notas y comunicaci&oacute;n con padres en una sola plataforma. Reportes para direcci&oacute;n al instante.</p>
            <div class="bot-tags"><span class="bot-tag">Matr&iacute;cula online</span><span class="bot-tag">Pensiones al d&iacute;a</span></div>
        </div>
        </div>
    </div>
    </section><!-- CLIENTES -->
    <section id="clientes" style="padding-top:0">
    <div class="container">
        <div class="section-head center reveal">
        <div class="section-label"><i class="fas fa-trophy"></i> Casos de &Eacute;xito</div>
        <h2 class="section-title">Lo que dicen nuestros clientes</h2>
        <p class="section-sub">Empresas de todos los sectores conf&iacute;an en ESTELAR para transformar su operaci&oacute;n digital.</p>
        </div>
        <!-- Drift Wall: muro de logos de clientes en perspectiva.
             Las teselas se declaran en el script (ITEMS). -->
        <div class="drift-wall" id="cliWall" role="group" aria-label="Clientes"></div>
        <!-- Stack: mazo de tarjetas. Arrastra, pulsa, o espera 10s. -->
        <div class="st-stack" id="cliStack">
            <div class="st-rotate" data-i="0">
            <div class="card st-card">
            <div class="cli-top">
                <div class="cli-avatar">MR</div>
                <div><div class="cli-name">Mar&iacute;a Rodr&iacute;guez</div><div class="cli-role">Gerente Operaciones &middot; LogisPer&uacute;</div></div>
                </div>
                <p class="cli-quote">"ESTELAR redujo nuestros tiempos de procesamiento de pedidos en un 65%. El ROI fue visible desde el primer mes."</p>
                <div class="cli-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            </div>
            </div>
            <div class="st-rotate" data-i="1">
            <div class="card st-card">
            <div class="cli-top">
                <div class="cli-avatar">CL</div>
                <div><div class="cli-name">Carlos Lira</div><div class="cli-role">CTO &middot; Grupo Comercial Andino</div></div>
                </div>
                <p class="cli-quote">"La integraci&oacute;n con nuestro ERP fue impecable. En 3 semanas ten&iacute;amos todos los procesos automatizados."</p>
                <div class="cli-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            </div>
            </div>
            <div class="st-rotate" data-i="2">
            <div class="card st-card">
            <div class="cli-top">
                <div class="cli-avatar">AP</div>
                <div><div class="cli-name">Ana Paredes</div><div class="cli-role">Directora RRHH &middot; BancoPyme</div></div>
                </div>
                <p class="cli-quote">"El bot de RRHH ahorra 15 horas semanales a nuestro equipo. El onboarding ahora es completamente digital."</p>
                <div class="cli-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            </div>
            </div>
        </div>
        <div class="st-dots" id="cliDots" aria-hidden="true">
            <i class="st-dot"></i>
            <i class="st-dot"></i>
            <i class="st-dot"></i>
        </div>
    </div>
    </section>

    <!-- PLANES -->
    <section id="planes" style="padding-top:0">
    <div class="container">
        <div class="section-head center reveal">
        <div class="section-label"><i class="fas fa-tag"></i> Precios</div>
        <h2 class="section-title">Planes para cada empresa</h2>
        <p class="section-sub">Sin sorpresas. Todos los planes incluyen soporte prioritario, actualizaciones autom&aacute;ticas y 30 d&iacute;as de prueba gratuita.</p>
        <!-- Selector de moneda. La eleccion se recuerda en localStorage. -->
        <div class="cur-switch" id="curSwitch" role="radiogroup" aria-label="Moneda">
            <button type="button" role="radio" data-cur="pen" aria-checked="true">S/. Soles</button>
            <button type="button" role="radio" data-cur="usd" aria-checked="false">US$ D&oacute;lares</button>
            <button type="button" role="radio" data-cur="cop" aria-checked="false">$ Pesos COP</button>
        </div>
        </div>
        <div class="fld-wrap">
        <!-- Folder: pulsa para desplegar los planes -->
        <div class="folder" id="planFolder" tabindex="0" role="button"
             aria-expanded="false" aria-label="Abrir planes">
            <div class="folder__back">
            <div class="paper">
                <div class="plan-card">
            <div class="plan-name">Starter</div>
                <div class="plan-price" data-pen="250" data-usd="69" data-cop="276000"><sup class="plan-cur">S/.</sup><span class="plan-amt">250</span></div>
                <div class="plan-period">por mes &middot; facturado mensualmente</div>
                <hr class="plan-divider">
                <ul class="plan-features">
                <li><i class="fas fa-check"></i> Hasta 3 bots activos</li>
                <li><i class="fas fa-check"></i> 5 usuarios incluidos</li>
                <li><i class="fas fa-check"></i> Integraciones medidas</li>
                <li><i class="fas fa-check"></i> 10 GB almacenamiento</li>
                <li><i class="fas fa-check"></i> Soporte t&eacute;cnico</li>
                </ul>
                <a href="#contacto" class="btn-ghost">Comenzar Gratis</a>
                </div>
            </div>
            <div class="paper">
                <div class="plan-card">
            <div class="plan-name">Enterprise</div>
                <div class="plan-price" style="font-size:2.2rem;padding-top:.5rem">A medida</div>
                <div class="plan-period">precio personalizado</div>
                <hr class="plan-divider">
                <ul class="plan-features">
                <li><i class="fas fa-check"></i> Todo lo del plan Pro</li>
                <li><i class="fas fa-check"></i> Usuarios ilimitados</li>
                <li><i class="fas fa-check"></i> Integraciones custom</li>
                <li><i class="fas fa-check"></i> SLA garantizado 99.9%</li>
                <li><i class="fas fa-check"></i> Account Manager dedicado</li>
                <li><i class="fas fa-check"></i> Instalaci&oacute;n on-premise</li>
                </ul>
                <a href="#contacto" class="btn-ghost">Contactar Ventas</a>
                </div>
            </div>
            <div class="paper">
                <div class="plan-card featured">
            <div class="plan-popular">MÁS POPULAR</div>
                <div class="plan-name">Professional</div>
                <div class="plan-price" data-pen="800" data-usd="220" data-cop="880000"><sup class="plan-cur">S/.</sup><span class="plan-amt">800</span></div>
                <div class="plan-period">por mes &middot; facturado mensualmente</div>
                <hr class="plan-divider">
                <ul class="plan-features">
                <li><i class="fas fa-check"></i> Hasta 10 bots activos</li>
                <li><i class="fas fa-check"></i> 25 usuarios incluidos</li>
                <li><i class="fas fa-check"></i> Integraciones medidas</li>
                <li><i class="fas fa-check"></i> 200 GB almacenamiento</li>
                <li><i class="fas fa-check"></i> Soporte prioritario 24/7</li>
                <li><i class="fas fa-check"></i> Analytics avanzados</li>
                </ul>
                <a href="#contacto" class="btn-primary"><i class="fas fa-rocket"></i> Empezar Ahora</a>
                </div>
            </div>
            <div class="folder__front"></div>
            <div class="folder__front right"></div>
            </div>
        </div>
        <span class="fld-hint">Pulsa la carpeta para ver los planes</span>
        </div>
    </div>
    </section><!-- CTA -->
    <section style="padding-top:0;padding-bottom:4rem">
    <div class="container">
        <div class="pc-card-wrapper reveal" id="ctaCard">
        <div class="pc-behind" aria-hidden="true"></div>
        <div class="pc-card-shell">
            <section class="pc-card">
            <div class="pc-inside">
                <div class="pc-shine" aria-hidden="true"></div>
                <div class="pc-glare" aria-hidden="true"></div>
                <div class="pc-content">
                <div class="cta-wrap">
                <div class="section-label" style="justify-content:center;margin-bottom:1rem"><i class="fas fa-bolt"></i> &iquest;Listo para empezar?</div>
                <h2 class="section-title">Transforma tu empresa hoy</h2>
                <p class="cta-lead">&Uacute;nete a m&aacute;s de 850 empresas que ya automatizaron sus operaciones con ESTELAR.</p>
                <div class="cta-btns">
                    <a href="#contacto" class="btn-primary"><i class="fas fa-rocket"></i> Solicitar Demo</a>
                    <a href="#planes" class="btn-ghost">Ver Planes <i class="fas fa-arrow-right"></i></a>
                </div>
                </div>
                </div>
            </div>
            </section>
        </div>
        </div>
    </div>
    </section>

    <!-- CONTACTO -->
    <section id="contacto" style="padding-top:0">
    <div class="container">
        <div class="section-head center reveal">
        <div class="section-label"><i class="fas fa-envelope"></i> Contacto</div>
        <h2 class="section-title">Hablemos de tu proyecto</h2>
        <p class="section-sub">Nuestro equipo te responder&aacute; en menos de 2 horas h&aacute;biles con una propuesta personalizada.</p>
        </div>
        <div class="contact-form reveal">
        <div class="form-row">
            <div class="form-group"><label>Nombre</label><input type="text" id="cf-nombre" placeholder="Tu nombre completo"></div>
            <div class="form-group"><label>Empresa</label><input type="text" id="cf-empresa" placeholder="Nombre de tu empresa"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Email</label><input type="email" id="cf-email" placeholder="correo@empresa.com"></div>
            <div class="form-group"><label>Tel&eacute;fono</label><input type="tel" id="cf-telefono" placeholder="+51 999 999 999"></div>
        </div>
        <div class="form-group">
            <label>&iquest;Qu&eacute; quieres automatizar?</label>
            <select id="cf-sistema-interes">
            <option value="">Selecciona un &aacute;rea</option>
            <option>Atenci&oacute;n al Cliente</option>
            <option>Facturaci&oacute;n y Cobranzas</option>
            <option>RRHH y Onboarding</option>
            <option>Inventario y Log&iacute;stica</option>
            <option>Marketing y Email</option>
            <option>An&aacute;lisis de Datos</option>
            <option>Otro</option>
            </select>
        </div>
        <div class="form-group"><label>Mensaje</label><textarea id="cf-mensaje" placeholder="Cu&eacute;ntanos sobre tu empresa y sus necesidades..."></textarea></div>
        <button class="btn-primary" id="submitBtn">
            <i class="fas fa-paper-plane"></i> Enviar Solicitud
        </button>
        <p id="formMsg" style="display:none;text-align:center;margin-top:1rem;color:var(--blue-glow);font-size:.9rem"></p>
        </div>
    </div>
    </section>
    </div><!-- /.main-container -->

    <!-- FOOTER -->
    <footer>
    <div class="footer-inner">
        <div class="footer-brand">
        <a href="#inicio" class="logo-wrap">
            <div style="height:50px;display:flex;align-items:center"><img src="img/logo_final.png" alt="ESTELAR" height="50" style="height:50px;width:auto;display:block;object-fit:contain"></div>
        </a>
        <p>Plataforma de automatizaci&oacute;n empresarial con IA. Transformamos operaciones en ventajas competitivas.</p>
        <div class="social-links" style="margin-top:1.2rem">
            <a href="#"><i class="fab fa-linkedin"></i></a>
            <a href="#"><i class="fab fa-github"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-whatsapp"></i></a>
        </div>
        </div>
        <div class="footer-col"><h4>Producto</h4><ul>
        <li><a href="#caracteristicas">Caracter&iacute;sticas</a></li>
        <li><a href="#bots">Bots &amp; IA</a></li>
        <li><a href="#planes">Planes</a></li>
        <li><a href="#">API Docs</a></li>
        <li><a href="#">Changelog</a></li>
        </ul></div>
        <div class="footer-col"><h4>Empresa</h4><ul>
        <li><a href="#">Nosotros</a></li>
        <li><a href="#">Blog</a></li>
        <li><a href="#clientes">Casos de &Eacute;xito</a></li>
        <li><a href="#">Partners</a></li>
        <li><a href="#contacto">Contacto</a></li>
        </ul></div>
        <div class="footer-col"><h4>Legal</h4><ul>
        <li><a href="#">T&eacute;rminos de Uso</a></li>
        <li><a href="#">Privacidad</a></li>
        <li><a href="#">Cookies</a></li>
        <li><a href="#">SLA</a></li>
        </ul></div>
    </div>
    <div class="footer-bottom">
        <span>&copy; 2026 ESTELAR Software S.A.C. &mdash; Huancayo, Per&uacute; &middot; Bogot&aacute;, Colombia</span>
        <span>Todos los derechos reservados.</span>
    </div>
    </footer>

    <!-- Redes sociales: fija en la esquina inferior derecha, en toda la
         pagina. Cuelga del body para que ningun contenedor la recorte ni
         se le ponga por encima. Pon aqui las URLs. -->
    <nav class="lk-social" data-rise="4" aria-label="Redes sociales">
    <a href="https://wa.me/51924210341" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
    <a href="#" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
    <a href="https://www.tiktok.com/@estelarsoftware?_r=1&_t=ZS-99woxJ91v5J" target="_blank" rel="noopener" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
    <a href="https://www.facebook.com/estelarsoftware1" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
    </nav>

    <!-- Moneda "Eventos": esquina inferior izquierda. Cuelga del body igual
         que .lk-social. El href apunta a la pagina propia del evento -ver
         resources/views/eventos/asistentes/showcase.blade.php-, que sigue
         sirviendo de fallback sin JS/compartible; con JS el click se
         intercepta (mas abajo) y en su lugar abre el panel deslizante
         montado en #eventsPanelRoot (resources/js/go-left-panel-mount.jsx).
         Sin evento real todavia no hay a donde enlazar, asi que no se
         pinta. Logica de spin/sprint de hover en el <script> de abajo. -->
    @if($goLeftEvent)
    <a href="{{ route('eventos.showcase', $goLeftEvent) }}" class="lk-coin" id="eventsCoin" aria-label="Ver eventos">
    <span class="lk-coin-spin" aria-hidden="true">
        <span class="lk-coin-edge" id="coinEdge"></span>
        <span class="lk-coin-face lk-coin-face--a"><img src="img/logo_icon_white.png" alt="" loading="lazy"></span>
        <span class="lk-coin-face lk-coin-face--b">Eventos</span>
    </span>
    </a>
    <div id="eventsPanelRoot"
        data-nombre="{{ $goLeftEvent->nombre }}"
        data-fecha="{{ optional($goLeftEvent->fecha_inicio)->format('d/m/Y') ?? '' }}"
        data-descripcion="{{ $goLeftEvent->descripcion ?? '' }}"
        data-banner="{{ asset('img/Banner28.png') }}"
        data-ticket-image="{{ asset('img/Go Left Estelar.jpeg') }}"
        data-video="{{ asset('video/VideoLeft.mp4') }}"
        data-bases="{{ asset('docs/Bases-GoLeft.pdf') }}"
        data-action="{{ route('eventos.inscripcion.store', $goLeftEvent) }}"
        data-csrf="{{ csrf_token() }}"
    ></div>
    @endif
    <script>
    /* ---- MONEDA "EVENTOS": gira, acelera al pasar el mouse ---- */
    (function () {
    const coin = document.getElementById('eventsCoin');
    if (!coin) return;

    /* Canto de la moneda: N franjas angostas en abanico alrededor del eje
     * Y, cada una a la misma distancia del centro (el radio) para formar
     * un tambor. Con solo CSS esto no sale -hay que repetir el segmento
     * tantas veces como el ojo necesite para leerlo como curvo, y el
     * ancho/radio dependen del tamaño ya renderizado (--coin-sz cambia
     * por breakpoint)-, asi que se arma una vez por JS y se reconstruye
     * si la ventana cambia de tamaño. El sombreado por segmento -mas
     * claro de frente, mas oscuro de perfil- es el mismo truco de
     * siempre para simular una fuente de luz fija sobre algo que gira. */
    function buildCoinEdge() {
        const edge = document.getElementById('coinEdge');
        if (!edge) return;
        const size = coin.getBoundingClientRect().width;
        if (!size) return;
        const radius = size / 2;
        const thickness = Math.round(size * 0.16);
        coin.style.setProperty('--coin-thick', thickness + 'px');

        /* La cara de la moneda vive en el plano XY (por eso rotateY la
         * "voltea" como card-flip). Su canto, entonces, es un aro en ESE
         * mismo plano XY -no un tambor alrededor de Y como un carrusel-,
         * asi que cada segmento va: al radio y angulo que le toca en ese
         * aro (translate3d), mirando hacia afuera (rotateZ) y con su alto
         * corrido al eje Z para que calce con el espesor (rotateX(90)).
         * El orden importa: rotateX se aplica primero (va al final de la
         * lista) y translate3d al final (va primero), fijando el punto
         * antes de orientar el segmento sobre el mismo. */
        const N = 28;
        const chord = (2 * radius * Math.sin(Math.PI / N)) + 0.75;
        const frag = document.createDocumentFragment();
        for (let i = 0; i < N; i++) {
        const deg = (360 / N) * i;
        const rad = (deg * Math.PI) / 180;
        const x = radius * Math.cos(rad);
        const y = radius * Math.sin(rad);
        const seg = document.createElement('span');
        seg.className = 'lk-coin-edge-seg';
        seg.style.width = chord + 'px';
        seg.style.height = thickness + 'px';
        seg.style.marginLeft = (-chord / 2) + 'px';
        seg.style.marginTop = (-thickness / 2) + 'px';
        seg.style.transform =
            `translate3d(${x}px, ${y}px, 0) rotateZ(${deg + 90}deg) rotateX(90deg)`;
        frag.appendChild(seg);
        }
        edge.replaceChildren(frag);
    }
    buildCoinEdge();
    let edgeResizeTimer = null;
    window.addEventListener('resize', () => {
        clearTimeout(edgeResizeTimer);
        edgeResizeTimer = setTimeout(buildCoinEdge, 150);
    });

    /* Sprint de 1s al hover, la moneda se asienta mostrando "EVENTOS"
     * (cara de atras) como adelanto, y vuelve a girar sola a los 2s. */
    let sprinting = false;
    let settled = false;
    let revertTimer = null;

    function settleCoin() {
        coin.classList.remove('is-fast');
        coin.classList.add('is-settled');
        sprinting = false;
        settled = true;
        clearTimeout(revertTimer);
        revertTimer = setTimeout(resumeSpin, 2000);
    }
    function resumeSpin() {
        clearTimeout(revertTimer);
        coin.classList.remove('is-settled', 'is-fast');
        sprinting = false;
        settled = false;
    }

    coin.addEventListener('mouseenter', () => {
        if (sprinting || settled) return;
        sprinting = true;
        coin.classList.add('is-fast');
        setTimeout(settleCoin, 1000);
    });

    /* El click ya no navega: abre el panel de Eventos (React, montado en
     * #eventsPanelRoot por resources/js/go-left-panel-mount.jsx), que
     * se desliza desde la derecha sobre el propio landing. El href
     * sigue apuntando a la pagina standalone del evento como fallback
     * si el JS no llegó a cargar. */
    coin.addEventListener('click', e => {
        e.preventDefault();
        document.dispatchEvent(new CustomEvent('eventos:open'));
    });
    })();

    /* ---- MOBILE NAV ---- */
    const mobileToggle = document.getElementById('mobileToggle');
    const navLinks = document.getElementById('navLinks');
    mobileToggle.addEventListener('click', () => {
    const open = navLinks.classList.toggle('open');
    mobileToggle.querySelector('i').className = open ? 'fas fa-times' : 'fas fa-bars';
    });
    document.querySelectorAll('.nav-links a').forEach(a => {
    a.addEventListener('click', () => {
        navLinks.classList.remove('open');
        mobileToggle.querySelector('i').className = 'fas fa-bars';
    });
    });

    /* ---- NAV SCROLL ---- */
    window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 40);
    });

    /* ---- SMOOTH SCROLL ---- */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const t = document.querySelector(a.getAttribute('href'));
        if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth' }); }
    });
    });

    /* ---- HERO SLIDES ---- */
    /* Retirado: el carrusel de 3 slides lo sustituye la composicion
       Logo wall sobre la escena Vortex. */

    /* ---- MATRIX RAIN + NODE NETWORK ---- */
    /* Retirado con el restyle: el suelo de la pagina es negro y la
       lluvia binaria peleaba con las placas de cristal. Dejarla habria
       mantenido un rAF a 60fps dibujando sobre un canvas oculto. */

    /* ---- COUNTERS ---- */
    function animCounter(el, end, suf) {
    const step = end / (1600 / 16);
    let cur = 0;
    const t = setInterval(() => {
        cur = Math.min(cur + step, end);
        el.textContent = Math.floor(cur).toLocaleString() + suf;
        if (cur >= end) clearInterval(t);
    }, 16);
    }
    const seen = new Set();
    const cObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting && !seen.has(e.target)) {
        seen.add(e.target);
        animCounter(e.target, parseInt(e.target.dataset.count), e.target.dataset.suffix || '+');
        }
    });
    }, { threshold: 0.4 });
    document.querySelectorAll('[data-count]').forEach(el => cObs.observe(el));

    /* ---- SCROLL REVEAL ---- */
    const rObs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); rObs.unobserve(e.target); } });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach((el, i) => {
    el.style.transitionDelay = (i % 6) * 80 + 'ms';
    rObs.observe(el);
    });

    /* ---- FORM ---- */
    document.getElementById('submitBtn').addEventListener('click', function() {
    const msg = document.getElementById('formMsg');
    const btn = this;
    const original = btn.innerHTML;

    const nombre = document.getElementById('cf-nombre').value.trim();
    if (!nombre) {
        msg.style.display = 'block';
        msg.style.color = '#f87171';
        msg.textContent = 'Por favor ingresa tu nombre.';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

    fetch('{{ route('landing.solicitud-demo') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            nombre: nombre,
            empresa: document.getElementById('cf-empresa').value.trim(),
            email: document.getElementById('cf-email').value.trim(),
            telefono: document.getElementById('cf-telefono').value.trim(),
            sistema_interes: document.getElementById('cf-sistema-interes').value,
            mensaje: document.getElementById('cf-mensaje').value.trim(),
        }),
    })
    .then(res => { if (!res.ok) throw new Error('request-failed'); return res.json(); })
    .then(() => {
        btn.innerHTML = '<i class="fas fa-check"></i> Solicitud Enviada';
        btn.style.background = 'linear-gradient(135deg,#4ade80,#16a34a)';
        btn.style.color = '#060912';
        msg.style.display = 'block';
        msg.style.color = 'var(--blue-glow)';
        msg.textContent = 'Recibimos tu solicitud. Te contactaremos en menos de 2 horas habiles.';
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = original;
        msg.style.display = 'block';
        msg.style.color = '#f87171';
        msg.textContent = 'No pudimos enviar tu solicitud. Intenta nuevamente.';
    });
    });
    </script>
        <script type="importmap">
    {
        "imports": {
        "three": "https://unpkg.com/three@0.143.0/build/three.module.js",
        "three/addons/": "https://unpkg.com/three@0.143.0/examples/jsm/"
        }
    }
    </script>

    <script type="module">
    /* ══════════════════════════════════════════════════════════════════
     * VORTEX — escena Three.js r0.143.0
     * Una estrella blanca de cuatro puntas en el centro, rodeada por un
     * vortice de 360.000 estelas GPU. Cada punto monta un filamento radial
     * en espiral que se retuerce alrededor de un anillo interior hueco;
     * pulsos de brillo corren del nucleo al borde mientras un giro lento
     * revuelve todo el halo.
     * ══════════════════════════════════════════════════════════════════ */
    import * as THREE from 'three'
    import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js'
    import { RenderPass } from 'three/addons/postprocessing/RenderPass.js'
    import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js'
    import { ShaderPass } from 'three/addons/postprocessing/ShaderPass.js'
    import { GammaCorrectionShader } from 'three/addons/shaders/GammaCorrectionShader.js'
    import { CopyShader } from 'three/addons/shaders/CopyShader.js'

    const CONFIG = {
        filaments: 600,
        perFilament: 600,
        coreRadius: 1.83,
        maxRadius: 15,
        radialExpo: 1.75,
        swirl: 0.8,
        spread: 0.89,
        depth: 2.8,
        curlAmp: 0,
        curlScale: 0.04,
        curlGrow: 1.45,
        flowSpeed: 0.22,
        streamSpeed: 3.8,
        pulseFreq: 8,
        coreFall: 0.02,
        twinkleSpeed: 2.75,
        flipSpeed: 4,
        glyphMix: 0.4,
        pointSize: 2.4,
        coreBoost: 8,
        brightness: 2.3,
        saturation: 0.56,
        digitSharp: 1.5,
        haloAmt: 0.8,
        haloFalloff: 1,
        coreColor: '#eaf3ff',
        strandColor: '#aecbff',
        sparkColor: '#bfe6ff',
        coreGlow: 0.35,
        coreSize: 8.7,
        coreSpikes: 0.74,
        autoSpin: 0.105,
        tilt: 0.58,
        parallax: 0.6,
        opacity: 1,
        bgColor: '#0a1326',
        bgColor2: '#04060f',
        coreBg: '#16213f',
        flameColor: '#3a78ff',
        flameColor2: '#9fe0ff',
        flameAmt: 0.22,
        starCount: 320,
        starColor: '#cfe2ff',
        starSize: 2,
        starTwinkle: 1.2,
        atmoColor: '#bcd6ff',
        atmoCount: 160,
        atmoSize: 14,
        atmoSpeed: 1,
    }

    function hexToVec3(hex) {
        const n = parseInt(hex.slice(1), 16)
        return new THREE.Vector3(((n >> 16) & 255) / 255, ((n >> 8) & 255) / 255, (n & 255) / 255)
    }
    const Lerp = (a, b, t) => a + (b - a) * t
    const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v))

    const canvas = document.getElementById('scene')

    /* ADAPTACION: el original es a ventana completa y mide con
     * innerWidth/innerHeight. Aqui el canvas vive dentro de un hero de
     * 100svh, asi que medimos el propio canvas — si no, el buffer de dibujo
     * se estiraria contra su caja CSS y deformaria la geometria en movil. */
    const sizeOf = () => ({
        w: canvas.clientWidth || window.innerWidth,
        h: canvas.clientHeight || window.innerHeight,
    })

    let { w: W, h: H } = sizeOf()
    let dpr = window.devicePixelRatio

    const renderer = new THREE.WebGL1Renderer({ canvas, antialias: true })
    renderer.setPixelRatio(dpr)
    renderer.setSize(W, H, false)
    renderer.shadowMap.enabled = true
    renderer.shadowMap.type = THREE.VSMShadowMap

    const scene = new THREE.Scene()
    scene.background = new THREE.Color(0x000000)
    scene.fog = null

    const camera = new THREE.PerspectiveCamera(52, W / H, 0.1, 240)
    camera.position.set(0, 0, 17)
    scene.add(camera)

    const LAYERS = { NONE: 0, TORUS_SCENE: 1, BLOOM_SCENE: 2, ENTIRE_SCENE: 3 }
    camera.layers.enable(LAYERS.TORUS_SCENE)
    camera.layers.enable(LAYERS.BLOOM_SCENE)
    camera.layers.enable(LAYERS.ENTIRE_SCENE)

    /* ── Geometria de los filamentos ────────────────────────────────────
     * La posicion real se calcula en el vertex shader; el atributo
     * `position` es un Float32Array de ceros de relleno. Cada hebra recibe
     * un angulo base aleatorio (de ahi los grumos y huecos organicos) y una
     * semilla propia; cada punto lleva su parametro 0->1 del anillo
     * interior al borde. */
    function buildFilaments(filaments, perFilament) {
        const N = filaments * perFilament
        const pos = new Float32Array(N * 3)
        const aAngle = new Float32Array(N)
        const aFil = new Float32Array(N)
        const aS = new Float32Array(N)
        const aSeed = new Float32Array(N)
        const aGlyph = new Float32Array(N)
        let k = 0
        for (let f = 0; f < filaments; f++) {
        const ang = Math.random() * Math.PI * 2
        const filSeed = Math.random()
        for (let p = 0; p < perFilament; p++) {
            aAngle[k] = ang
            aFil[k] = filSeed
            aS[k] = perFilament > 1 ? p / (perFilament - 1) : 0
            aSeed[k] = Math.random()
            aGlyph[k] = Math.random()
            k++
        }
        }
        const g = new THREE.BufferGeometry()
        g.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3))
        g.setAttribute('aAngle', new THREE.Float32BufferAttribute(aAngle, 1))
        g.setAttribute('aFil', new THREE.Float32BufferAttribute(aFil, 1))
        g.setAttribute('aS', new THREE.Float32BufferAttribute(aS, 1))
        g.setAttribute('aSeed', new THREE.Float32BufferAttribute(aSeed, 1))
        g.setAttribute('aGlyph', new THREE.Float32BufferAttribute(aGlyph, 1))
        return g
    }

    /* Atlas de glifos binarios — un canvas de 2 celdas (0 | 1) que se
     * muestrea como sprite del punto para la minoria de mota binaria. */
    function makeGlyphTexture() {
        const S = 128
        const cv = document.createElement('canvas'); cv.width = S * 2; cv.height = S
        const c = cv.getContext('2d')
        c.clearRect(0, 0, S * 2, S)
        c.fillStyle = '#fff'; c.textAlign = 'center'; c.textBaseline = 'middle'
        c.font = 'bold ' + Math.floor(S * 0.82) + 'px ui-monospace, Menlo, monospace'
        c.fillText('0', S * 0.5, S * 0.54)
        c.fillText('1', S * 1.5, S * 0.54)
        const tex = new THREE.CanvasTexture(cv)
        tex.minFilter = THREE.LinearFilter; tex.magFilter = THREE.LinearFilter
        return tex
    }

    const glyphTex = makeGlyphTexture()

    const filMat = new THREE.ShaderMaterial({
        transparent: true,
        depthTest: false,
        depthWrite: false,
        toneMapped: false,
        blending: THREE.AdditiveBlending,
        uniforms: {
        iTime: { value: 0 },
        uAlpha: { value: 0 },
        uRes: { value: new THREE.Vector2(W * dpr, H * dpr) },
        uGlyphTex: { value: glyphTex },
        uCoreR: { value: CONFIG.coreRadius },
        uMaxR: { value: CONFIG.maxRadius },
        uExpo: { value: CONFIG.radialExpo },
        uSwirl: { value: CONFIG.swirl },
        uSpread: { value: CONFIG.spread },
        uDepth: { value: CONFIG.depth },
        uCurlAmp: { value: CONFIG.curlAmp },
        uCurlScale: { value: CONFIG.curlScale },
        uCurlGrow: { value: CONFIG.curlGrow },
        uFlowSpeed: { value: CONFIG.flowSpeed },
        uStreamSpeed: { value: CONFIG.streamSpeed },
        uPulseFreq: { value: Math.round(CONFIG.pulseFreq) },
        uCoreFall: { value: CONFIG.coreFall },
        uTwinkle: { value: CONFIG.twinkleSpeed },
        uFlipSpeed: { value: CONFIG.flipSpeed },
        uGlyphMix: { value: CONFIG.glyphMix },
        uSize: { value: CONFIG.pointSize },
        uCoreBoost: { value: CONFIG.coreBoost },
        uBright: { value: CONFIG.brightness },
        uSat: { value: CONFIG.saturation },
        uDigitSharp: { value: CONFIG.digitSharp },
        uHaloAmt: { value: CONFIG.haloAmt },
        uHaloFall: { value: CONFIG.haloFalloff },
        uCore: { value: hexToVec3(CONFIG.coreColor) },
        uStrand: { value: hexToVec3(CONFIG.strandColor) },
        uSpark: { value: hexToVec3(CONFIG.sparkColor) },
        },
        vertexShader: `
    attribute float aAngle; attribute float aFil; attribute float aS; attribute float aSeed; attribute float aGlyph;
    uniform float iTime, uAlpha;
    uniform vec2 uRes;
    uniform float uCoreR, uMaxR, uExpo, uSwirl, uSpread, uDepth, uCurlAmp, uCurlScale, uCurlGrow, uFlowSpeed;
    uniform float uStreamSpeed, uPulseFreq, uCoreFall, uTwinkle, uFlipSpeed, uGlyphMix, uSize, uCoreBoost, uBright, uSat;
    uniform vec3 uCore, uStrand, uSpark;
    varying vec3 vCol; varying float vBright; varying float vGlyph; varying float vDigit;
    #define TAU 6.2831853

    // smooth feedback domain-warp — coherent in position, so neighbouring
    // points on a strand braid into one continuous waving filament.
    vec3 flow(vec3 p, float t){
    vec3 q = p;
    q.x += 0.9 * sin(t + 1.7 * p.y);
    q.y += 0.9 * cos(t + 1.7 * p.z);
    q.z += 0.9 * sin(t + 1.7 * p.x);
    q.x += 0.5 * cos(t * 1.3 + 2.9 * q.z);
    q.z += 0.5 * sin(t * 1.1 + 2.9 * q.y);
    return q - p;
    }
    float hash(float n){ return fract(sin(n) * 43758.5453123); }

    void main(){
    float s = aS;                                            // 0 (core) .. 1 (rim)
    float radius = uCoreR + (uMaxR - uCoreR) * pow(s, uExpo);

    // spiral twist that grows outward + organic per-strand wander
    float ang = aAngle + uSwirl * radius * (0.6 + 0.4 * aFil);
    ang += sin(s * 6.0 + aFil * 30.0) * uSpread * (0.25 + 0.75 * s);

    vec3 dir = vec3(cos(ang), sin(ang), 0.0);
    vec3 pos = dir * radius;
    pos.z = (aFil - 0.5) * uDepth * (0.4 + radius * 0.12);

    // coherent curl that braids the strands and grows toward the rim
    vec3 fp = pos * uCurlScale + vec3(0.0, 0.0, aFil * 12.0);
    pos += flow(fp, iTime * uFlowSpeed) * uCurlAmp * (0.18 + uCurlGrow * s);

    vec4 mv = modelViewMatrix * vec4(pos, 1.0);

    // brightness: hot core falloff x outward-travelling pulse x twinkle x tip fade
    float coreGlow = exp(-radius * radius * uCoreFall);
    float pulse = 0.5 + 0.5 * sin(TAU * uPulseFreq * s - iTime * uStreamSpeed + aFil * 6.0);
    float tw = 0.7 + 0.3 * sin(iTime * uTwinkle + aSeed * 40.0);
    float tip = smoothstep(1.0, 0.72, s);
    vBright = (0.22 + 0.95 * coreGlow) * (0.45 + 0.75 * pulse) * tw * tip * uBright * uAlpha;

    // colour: cool strand, a sparkle minority gets iridescent hue, core whitens
    float hue = fract(aFil * 1.7 + s * 0.35);
    vec3 rainbow = 0.5 + 0.5 * cos(TAU * (hue + vec3(0.0, 0.33, 0.67)));
    vec3 spark = mix(uSpark, rainbow, uSat);
    vec3 col = mix(uStrand, spark, step(0.72, aSeed) * (0.4 + 0.6 * uSat));
    col = mix(col, uCore, coreGlow * 0.85);
    vCol = col;

    // animated binary glyph (flips on a per-point tick) + dot/digit split
    float tick = floor(iTime * uFlipSpeed + aGlyph * 7.0);
    vGlyph = step(0.5, hash(aGlyph * 91.7 + tick * 13.3));
    vDigit = (aSeed < uGlyphMix) ? 1.0 : 0.0;

    float size = uSize * (0.55 + uCoreBoost * coreGlow) * (0.7 + 0.6 * pulse);
    gl_PointSize = clamp(size * uRes.y / 1000.0 / -mv.z, 1.0, 64.0);
    gl_Position = projectionMatrix * mv;
    }
    `,
        fragmentShader: `
    uniform sampler2D uGlyphTex;
    uniform float uDigitSharp, uHaloAmt, uHaloFall;
    varying vec3 vCol; varying float vBright; varying float vGlyph; varying float vDigit;
    void main(){
    vec2 pc = gl_PointCoord;
    float d = length(pc - 0.5);
    float halo = pow(max(0.0, 1.0 - d * 2.0), uHaloFall) * uHaloAmt;   // soft round glow
    float mask = halo;
    if (vDigit > 0.5) {
        vec2 guv = vec2((pc.x + vGlyph) * 0.5, pc.y);                    // pick 0 | 1 cell from the atlas
        float g = texture2D(uGlyphTex, guv).r;
        mask = halo * 0.45 + g * uDigitSharp;                           // crisp digit over a faint glow
    }
    if (mask <= 0.001) discard;
    gl_FragColor = vec4(vCol * vBright, mask);
    }
    `,
    })

    const points = new THREE.Points(
        buildFilaments(Math.round(CONFIG.filaments), Math.round(CONFIG.perFilament)),
        filMat
    )
    points.frustumCulled = false
    points.layers.set(LAYERS.ENTIRE_SCENE)

    /* ── El destello del nucleo ─────────────────────────────────────── */
    const coreMat = new THREE.ShaderMaterial({
        transparent: true,
        depthTest: false,
        depthWrite: false,
        toneMapped: false,
        blending: THREE.AdditiveBlending,
        uniforms: {
        uColor: { value: hexToVec3(CONFIG.coreColor) },
        // Atenuado respecto al 0.35 de la spec: aquel valor era para la
        // estrella, que ERA el destello. Ahora hay un emblema solido delante
        // y a plena potencia le lavaba los cantos.
        uGlow: { value: CONFIG.coreGlow * 0.55 },
        uSpikes: { value: CONFIG.coreSpikes },
        uAlpha: { value: 0 },
        },
        vertexShader: `
    varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0); }
    `,
        fragmentShader: `
    uniform vec3 uColor; uniform float uGlow, uSpikes, uAlpha; varying vec2 vUv;
    void main(){
    vec2 p = vUv * 2.0 - 1.0;
    float r = length(p);
    float core = pow(max(0.0, 1.0 - r), 3.0);
    float halo = exp(-r * r * 3.0) * 0.7;
    // anamorphic cross flare
    float sx = exp(-abs(p.y) * 90.0) * exp(-abs(p.x) * 1.6);
    float sy = exp(-abs(p.x) * 90.0) * exp(-abs(p.y) * 1.6);
    float spikes = (sx + sy) * uSpikes;
    float i = (core * 2.0 + halo + spikes) * uGlow;
    gl_FragColor = vec4(uColor * i, i * uAlpha);
    }
    `,
    })

    const core = new THREE.Mesh(new THREE.PlaneBufferGeometry(1, 1), coreMat)
    core.scale.setScalar(CONFIG.coreSize * 2.0)
    core.layers.set(LAYERS.ENTIRE_SCENE)

    /* Los filamentos y el destello van en un solo grupo: es el grupo lo que
     * gira y se inclina. */
    const burst = new THREE.Group()
    burst.add(points)
    burst.add(core)
    scene.add(burst)

    /* ── El emblema de ESTELAR, extruido en 3D ──────────────────────────
     * Sustituye a la estrella del centro. Los contornos salen de trazar el
     * canal alfa de img/logo_final.png con marching squares y simplificarlos
     * con Douglas-Peucker: un contorno exterior (el hexagono) y dos agujeros
     * (las alas a ambos lados de la estrella). Coordenadas normalizadas al
     * lado mayor = 1 y centradas en el origen, con la y hacia arriba.
     *
     * El emblema ya lleva dentro una estrella de cuatro puntas, que es la
     * misma figura que dibujaba el destello: por eso el resplandor se queda
     * detras en vez de retirarse — sus aspas refuerzan las de la pieza. */
    const LOGO_SHAPE = {
      outer: [
        -0.0254,0.3184,0.0225,0.3193,0.0244,0.3174,0.0381,0.3174,0.04,0.3154,0.0498,0.3154,0.0518,0.3135,0.0596,0.3135,
        0.0615,0.3115,0.0752,0.3096,0.0771,0.3076,0.1045,0.2998,0.1201,0.292,0.124,0.292,0.1357,0.2861,0.1455,0.2842,
        0.1611,0.2764,0.165,0.2764,0.1768,0.2705,0.1865,0.2686,0.2021,0.2607,0.2061,0.2607,0.2178,0.2549,0.2275,0.2529,
        0.2334,0.249,0.2432,0.2471,0.249,0.2432,0.2529,0.2432,0.2588,0.2393,0.2686,0.2373,0.2842,0.2295,0.2939,0.2275,
        0.2998,0.2236,0.3096,0.2217,0.3252,0.2139,0.335,0.2119,0.3408,0.208,0.3506,0.2061,0.3662,0.1982,0.376,0.1963,
        0.3818,0.1924,0.3916,0.1904,0.4072,0.1826,0.417,0.1807,0.4326,0.1729,0.4424,0.1709,0.4482,0.167,0.4521,0.167,
        0.4639,0.1611,0.4678,0.1611,0.4736,0.1572,0.4775,0.1572,0.4854,0.1533,0.4961,0.1426,0.498,0.1348,0.5,0.1328,
        0.5,-0.1328,0.498,-0.1348,0.4961,-0.1426,0.4854,-0.1533,0.4736,-0.1592,0.4697,-0.1592,0.458,-0.165,0.4541,-0.165,
        0.4424,-0.1709,0.4385,-0.1709,0.4326,-0.1748,0.4287,-0.1748,0.417,-0.1807,0.4072,-0.1826,0.3916,-0.1904,0.3877,-0.1904,
        0.376,-0.1963,0.3662,-0.1982,0.3506,-0.2061,0.3467,-0.2061,0.335,-0.2119,0.3252,-0.2139,0.3096,-0.2217,0.3057,-0.2217,
        0.2939,-0.2275,0.2842,-0.2295,0.2686,-0.2373,0.2646,-0.2373,0.2529,-0.2432,0.2432,-0.2451,0.2275,-0.2529,0.2236,-0.2529,
        0.2119,-0.2588,0.2021,-0.2607,0.1865,-0.2686,0.1768,-0.2705,0.1709,-0.2744,0.167,-0.2744,0.1553,-0.2803,0.1455,-0.2822,
        0.1299,-0.29,0.126,-0.29,0.1143,-0.2959,0.1045,-0.2979,0.0986,-0.3018,0.0947,-0.3018,0.0928,-0.3037,0.0889,-0.3037,
        0.0869,-0.3057,0.083,-0.3057,0.0693,-0.3115,0.0635,-0.3115,0.0615,-0.3135,0.042,-0.3154,0.04,-0.3174,0.0244,-0.3174,
        0.0225,-0.3193,-0.0205,-0.3193,-0.0225,-0.3174,-0.0361,-0.3174,-0.0381,-0.3154,-0.0479,-0.3154,-0.0498,-0.3135,-0.0576,-0.3135,
        -0.0596,-0.3115,-0.0811,-0.3076,-0.083,-0.3057,-0.1104,-0.2979,-0.1162,-0.2939,-0.1201,-0.2939,-0.126,-0.29,-0.1357,-0.2881,
        -0.1514,-0.2803,-0.1611,-0.2783,-0.1768,-0.2705,-0.1807,-0.2705,-0.1924,-0.2646,-0.2021,-0.2627,-0.208,-0.2588,-0.2119,-0.2588,
        -0.2178,-0.2549,-0.2275,-0.2529,-0.2432,-0.2451,-0.2529,-0.2432,-0.2686,-0.2354,-0.2783,-0.2334,-0.2842,-0.2295,-0.2939,-0.2275,
        -0.3096,-0.2197,-0.3193,-0.2178,-0.3252,-0.2139,-0.335,-0.2119,-0.3506,-0.2041,-0.3604,-0.2021,-0.3662,-0.1982,-0.376,-0.1963,
        -0.3818,-0.1924,-0.3857,-0.1924,-0.3916,-0.1885,-0.4014,-0.1865,-0.417,-0.1787,-0.4268,-0.1768,-0.4326,-0.1729,-0.4424,-0.1709,
        -0.458,-0.1631,-0.4619,-0.1631,-0.4736,-0.1572,-0.4775,-0.1572,-0.4873,-0.1514,-0.4941,-0.1445,-0.5,-0.1309,-0.5,0.1328,
        -0.498,0.1348,-0.4961,0.1426,-0.4854,0.1533,-0.4775,0.1572,-0.4678,0.1592,-0.4619,0.1631,-0.458,0.1631,-0.4521,0.167,
        -0.4424,0.1689,-0.4268,0.1768,-0.417,0.1787,-0.4014,0.1865,-0.3916,0.1885,-0.376,0.1963,-0.3662,0.1982,-0.3506,0.2061,
        -0.3467,0.2061,-0.335,0.2119,-0.3252,0.2139,-0.3096,0.2217,-0.3057,0.2217,-0.2939,0.2275,-0.2842,0.2295,-0.2686,0.2373,
        -0.2588,0.2393,-0.2432,0.2471,-0.2393,0.2471,-0.2275,0.2529,-0.2178,0.2549,-0.2021,0.2627,-0.1982,0.2627,-0.1865,0.2686,
        -0.1768,0.2705,-0.1611,0.2783,-0.1572,0.2783,-0.1455,0.2842,-0.1357,0.2861,-0.1201,0.2939,-0.1162,0.2939,-0.1045,0.2998,
        -0.0947,0.3018,-0.0889,0.3057,-0.083,0.3057,-0.0752,0.3096,-0.0693,0.3096,-0.0674,0.3115,-0.0615,0.3115,-0.0596,0.3135,
        -0.0537,0.3135,-0.0518,0.3154,-0.042,0.3154,-0.04,0.3174
      ],
      holes: [
        [
          -0.2002,0.0928,-0.2119,0.0869,-0.2158,0.0869,-0.2178,0.085,-0.2275,0.083,-0.2334,0.0791,-0.2432,0.0771,-0.2529,0.0713,
          -0.2568,0.0713,-0.2725,0.0635,-0.2822,0.0615,-0.2979,0.0537,-0.3076,0.0518,-0.3135,0.0479,-0.3174,0.0479,-0.3232,0.0439,
          -0.3271,0.0439,-0.3389,0.0381,-0.3516,0.0254,-0.3574,0.0137,-0.3594,-0.0039,-0.3574,-0.0059,-0.3574,-0.0117,-0.3555,-0.0137,
          -0.3535,-0.0215,-0.3408,-0.0361,-0.3311,-0.042,-0.3271,-0.042,-0.3115,-0.0498,-0.3018,-0.0518,-0.2861,-0.0596,-0.2822,-0.0596,
          -0.2705,-0.0654,-0.2607,-0.0674,-0.2549,-0.0713,-0.2451,-0.0732,-0.2295,-0.0811,-0.2197,-0.083,-0.2041,-0.0908,-0.1943,-0.0928,
          -0.1885,-0.0967,-0.1787,-0.0986,-0.1631,-0.1064,-0.1533,-0.1084,-0.1377,-0.1162,-0.1279,-0.1182,-0.1123,-0.126,-0.1025,-0.1279,
          -0.0967,-0.1318,-0.0928,-0.1318,-0.0771,-0.1396,-0.0732,-0.1396,-0.0713,-0.1416,-0.0674,-0.1416,-0.0654,-0.1436,-0.0615,-0.1436,
          -0.0479,-0.1494,-0.042,-0.1494,-0.04,-0.1514,-0.0342,-0.1514,-0.0322,-0.1533,-0.0225,-0.1533,-0.0205,-0.1553,-0.0029,-0.1553,
          -0.001,-0.1572,0.0068,-0.1572,0.0088,-0.1553,0.0264,-0.1553,0.0283,-0.1533,0.0498,-0.1494,0.0654,-0.1416,0.0693,-0.1416,
          0.0811,-0.1357,0.0908,-0.1338,0.1064,-0.126,0.1104,-0.126,0.1221,-0.1201,0.1318,-0.1182,0.1475,-0.1104,0.1572,-0.1084,
          0.1729,-0.1006,0.1768,-0.1006,0.1885,-0.0947,0.1982,-0.0928,0.2139,-0.085,0.2236,-0.083,0.2295,-0.0791,0.2334,-0.0791,
          0.2393,-0.0752,0.249,-0.0732,0.2549,-0.0693,0.2646,-0.0674,0.2705,-0.0635,0.2744,-0.0635,0.2803,-0.0596,0.2842,-0.0596,
          0.292,-0.0557,0.2998,-0.0537,0.3057,-0.0498,0.3154,-0.0479,0.3213,-0.0439,0.3252,-0.0439,0.3408,-0.0361,0.3496,-0.0273,
          0.3574,-0.0117,0.3574,0.0117,0.3516,0.0254,0.3389,0.0381,0.3232,0.0459,0.3135,0.0479,0.3076,0.0518,0.3037,0.0518,
          0.2979,0.0557,0.2881,0.0576,0.2822,0.0615,0.2783,0.0615,0.2725,0.0654,0.2627,0.0674,0.2568,0.0713,0.2471,0.0732,
          0.2412,0.0771,0.2373,0.0771,0.2314,0.0811,0.2217,0.083,0.2158,0.0869,0.2119,0.0869,0.21,0.0889,0.2002,0.0889,
          0.1953,0.084,0.1953,0.0781,0.1934,0.0762,0.1934,0.0645,0.1914,0.0625,0.1914,0.0527,0.1895,0.0508,0.1895,0.043,
          0.1875,0.041,0.1855,0.0273,0.1719,-0.0039,0.168,-0.0078,0.1621,-0.0195,0.1504,-0.0332,0.1504,-0.0352,0.1221,-0.0635,
          0.1201,-0.0635,0.1143,-0.0693,0.1123,-0.0693,0.0967,-0.0811,0.0811,-0.0889,0.0771,-0.0889,0.0713,-0.0928,0.0674,-0.0928,
          0.0596,-0.0967,0.0381,-0.1006,0.0361,-0.1025,0.0264,-0.1025,0.0244,-0.1045,0.0088,-0.1045,0.0068,-0.1064,-0.0186,-0.1064,
          -0.0205,-0.1045,-0.0342,-0.1045,-0.0361,-0.1025,-0.0518,-0.1006,-0.0537,-0.0986,-0.0693,-0.0947,-0.0967,-0.0811,-0.1006,-0.0771,
          -0.1143,-0.0693,-0.1318,-0.0537,-0.1338,-0.0537,-0.1543,-0.0312,-0.1543,-0.0293,-0.168,-0.0117,-0.1836,0.0215,-0.1836,0.0254,
          -0.1875,0.0332,-0.1875,0.0391,-0.1895,0.041,-0.1895,0.0469,-0.1914,0.0488,-0.1934,0.0742,-0.1953,0.0762,-0.1953,0.0898,
        ],
        [
          -0.001,0.2393,-0.0039,0.2363,-0.0039,0.2305,-0.0059,0.2285,-0.0078,0.209,-0.0098,0.207,-0.0098,0.1992,-0.0117,0.1973,
          -0.0117,0.1895,-0.0137,0.1875,-0.0137,0.1777,-0.0156,0.1758,-0.0156,0.168,-0.0176,0.166,-0.0176,0.1562,-0.0195,0.1543,
          -0.0195,0.1465,-0.0215,0.1445,-0.0215,0.1348,-0.0234,0.1328,-0.0234,0.127,-0.0273,0.1191,-0.0361,0.1104,-0.0439,0.1064,
          -0.0557,0.1045,-0.0576,0.1025,-0.0693,0.1006,-0.0713,0.0986,-0.0771,0.0986,-0.0791,0.0967,-0.085,0.0967,-0.0869,0.0947,
          -0.0928,0.0947,-0.0947,0.0928,-0.1006,0.0928,-0.1055,0.0898,-0.1055,0.0859,-0.0947,0.0811,-0.0889,0.0811,-0.0869,0.0791,
          -0.0811,0.0791,-0.0791,0.0771,-0.0732,0.0771,-0.0713,0.0752,-0.0654,0.0752,-0.0635,0.0732,-0.0576,0.0732,-0.0557,0.0713,
          -0.0439,0.0693,-0.0361,0.0654,-0.0293,0.0586,-0.0293,0.0566,-0.0254,0.0527,-0.0234,0.041,-0.0215,0.0391,-0.0215,0.0293,
          -0.0195,0.0273,-0.0195,0.0176,-0.0176,0.0156,-0.0156,-0.0039,-0.0137,-0.0059,-0.0137,-0.0137,-0.0117,-0.0156,-0.0117,-0.0254,
          -0.0098,-0.0273,-0.0098,-0.0352,-0.0078,-0.0371,-0.0059,-0.0586,-0.0029,-0.0635,0.001,-0.0635,0.0039,-0.0605,0.0039,-0.0527,
          0.0059,-0.0508,0.0059,-0.043,0.0078,-0.041,0.0078,-0.0332,0.0098,-0.0312,0.0098,-0.0234,0.0117,-0.0215,0.0117,-0.0117,
          0.0137,-0.0098,0.0137,-0.002,0.0156,-0,0.0156,0.0098,0.0176,0.0117,0.0176,0.0215,0.0195,0.0234,0.0215,0.043,
          0.0273,0.0566,0.0361,0.0654,0.0439,0.0693,0.0479,0.0693,0.0557,0.0732,0.0615,0.0732,0.0635,0.0752,0.0693,0.0752,
          0.0713,0.0771,0.0771,0.0771,0.0791,0.0791,0.085,0.0791,0.0869,0.0811,0.0928,0.0811,0.0947,0.083,0.1045,0.085,
          0.1055,0.0879,0.1025,0.0908,0.0947,0.0928,0.0928,0.0947,0.0869,0.0947,0.085,0.0967,0.0791,0.0967,0.0771,0.0986,
          0.0713,0.0986,0.0693,0.1006,0.0537,0.1025,0.0518,0.1045,0.042,0.1064,0.0293,0.1172,0.0293,0.1191,0.0254,0.123,
          0.0254,0.127,0.0215,0.1348,0.0215,0.1406,0.0195,0.1426,0.0195,0.1523,0.0176,0.1543,0.0176,0.1641,0.0156,0.166,
          0.0156,0.1738,0.0137,0.1758,0.0137,0.1855,0.0117,0.1875,0.0117,0.1953,0.0098,0.1973,0.0098,0.207,0.0078,0.209,
          0.0078,0.2168,0.0059,0.2188,0.0039,0.2363
        ],
      ],
    }

    const LOGO_SCALE = 5.6      // ancho del emblema en unidades de escena
    const LOGO_DEPTH = 0.16     // grosor de la extrusion, relativo al lado 1

    function buildLogoGeometry() {
      const toPts = (flat) => {
        const v = []
        for (let i = 0; i < flat.length; i += 2) v.push(new THREE.Vector2(flat[i], flat[i + 1]))
        return v
      }
      const shape = new THREE.Shape(toPts(LOGO_SHAPE.outer))
      for (const h of LOGO_SHAPE.holes) shape.holes.push(new THREE.Path(toPts(h)))

      const g = new THREE.ExtrudeGeometry(shape, {
        depth: LOGO_DEPTH,
        curveSegments: 4,
        steps: 1,
        bevelEnabled: true,
        bevelThickness: 0.016,
        bevelSize: 0.014,
        bevelOffset: 0,
        bevelSegments: 3,
      })
      g.center()                 // la extrusion nace en z=0; la centramos
      g.computeVertexNormals()
      return g
    }

    /* Acero azul, no blanco. Un emblema blanco delante de un nucleo blanco
     * se reventaba: sin volumen y con la estrella interior perdida. En metal
     * oscuro la pieza tiene forma, y los huecos — la estrella y las dos alas
     * — dejan pasar el nucleo ardiendo por detras, que es lo que la vuelve a
     * hacer legible. */
    const logoMat = new THREE.MeshPhysicalMaterial({
      color: 0xffffff,
      // Blanco y mate. Sin metal y sin barniz no hay reflejo especular que
      // recorra la pieza: la luz se reparte por igual y el volumen lo dan el
      // bisel y el sombreado, no el brillo. El emissive bajo solo evita que
      // la cara en sombra se vaya a negro.
      metalness: 0.0,
      roughness: 0.58,
      clearcoat: 0.0,
      emissive: 0x141414,
      emissiveIntensity: 0.22,
      // Transparente para entrar en la lista de transparentes y que
      // renderOrder mande: si fuese opaco, three lo dibujaria ANTES que los
      // filamentos aditivos y estos le pasarian por encima.
      transparent: true,
      opacity: 0,
      depthTest: true,
      depthWrite: true,
    })

    const logo = new THREE.Mesh(buildLogoGeometry(), logoMat)
    logo.scale.setScalar(LOGO_SCALE)
    logo.renderOrder = 3
    logo.layers.set(LAYERS.ENTIRE_SCENE)
    burst.add(logo)

    /* Luces. La escena no tenia ninguna — todo lo demas es aditivo y se
     * ilumina solo. El emblema si es geometria solida, asi que necesita con
     * que leerse: una luz clave al frente, un contraluz que le dibuje el
     * canto, y un acento teal de la propia marca. Van en ENTIRE_SCENE porque
     * three descarta las luces que no comparten capa con la camara. */
    const lights = new THREE.Group()
    /* Intensidades bajas a proposito. Con albedo blanco puro, cualquier luz
     * cerca de 1.0 satura la cara y el bloom (umbral 0: entra todo) remata la
     * faena: la pieza se vuelve un bulto plano y se pierden la estrella y las
     * alas. Manteniendola en torno al 60% de luminancia queda blanca pero
     * tenue, y el sombreado vuelve a contar el volumen.
     *
     * Y neutras de color: con luces azuladas el blanco se iba a lavanda.
     * Sobre un albedo blanco, el tinte de la luz ES el tinte de la pieza. */
    const key = new THREE.DirectionalLight(0xffffff, 0.62); key.position.set(4, 6, 9)
    const rim = new THREE.PointLight(0xffffff, 0.60, 60); rim.position.set(-8, -2, -6)
    const acc = new THREE.PointLight(0xffffff, 0.15, 45); acc.position.set(6, -4, 5)
    const amb = new THREE.AmbientLight(0x4f4f55, 0.85)
    for (const l of [key, rim, acc, amb]) { l.layers.set(LAYERS.ENTIRE_SCENE); lights.add(l) }
    scene.add(lights)

    /* ── Encuadre ───────────────────────────────────────────────────────
     * ADAPTACION: la estrella era un resplandor blando y el titular podia
     * pasarle por encima sin perderse. El emblema es solido y opaco, asi que
     * cortaba la segunda linea a media palabra. En ventanas anchas el
     * estallido se corre al medio libre que deja la columna de texto; en
     * vertical se queda centrado, porque no hay sitio al que moverlo. */
    // Media pantalla en unidades de escena, a la profundidad del estallido.
    const halfViewH = 17 * Math.tan((52 / 2) * Math.PI / 180)

    /* Donde acaba de verdad la tinta del hero, y solo la que se cruza con la
     * banda horizontal que ocupa el emblema. Dos precisiones que importan:
     *  - medimos los glifos con un Range, no la caja: la columna mide 1260px
     *    pero el titular termina mucho antes;
     *  - filtramos por altura, porque la primera linea del titular es la mas
     *    larga y ni siquiera pasa por delante del emblema. Contarla lo
     *    empujaba fuera de cuadro. */
    function heroInkRight(bandTop, bandBottom) {
      const sel = ['.lkx-h1 span', '.lkx-sub', '.lkx-cta']
      const r = document.createRange()
      let right = 0
      for (const s of sel) {
        for (const el of document.querySelectorAll(s)) {
          r.selectNodeContents(el)
          const box = r.getBoundingClientRect()
          if (!box.width) continue
          // Solapamiento real, no un roce: la primera linea del titular es
          // la mas larga y toca la banda por 25 de sus 120px de alto. Con un
          // umbral de rozar, mandaba el emblema contra el borde derecho.
          const ov = Math.min(box.bottom, bandBottom) - Math.max(box.top, bandTop)
          if (ov < box.height * 0.35) continue
          right = Math.max(right, box.right)
        }
      }
      return right
    }

    // Alto del emblema respecto a su ancho, tal cual sale de la silueta.
    logo.geometry.computeBoundingBox()
    const LOGO_BB = logo.geometry.boundingBox
    const LOGO_H_RATIO = LOGO_BB.max.y - LOGO_BB.min.y

    function frameBurst() {
      if (W > 860 && W / H > 1.2) {
        // Apaisado: el emblema se centra en el hueco que queda entre el
        // final del texto y el borde derecho. Asi acompana a los
        // breakpoints, donde el titular se come mas ancho proporcional.
        const pxPerUnit = H / (2 * halfViewH)
        const halfBand = (LOGO_SCALE * LOGO_H_RATIO / 2) * pxPerUnit + 24
        const ink = heroInkRight(H / 2 - halfBand, H / 2 + halfBand) || W * 0.5
        const halfLogo = LOGO_SCALE / 2
        let x = ((ink + W) / 2 - W / 2) / pxPerUnit
        x = Math.min(x, (W / 2) / pxPerUnit - halfLogo - 1.0)  // que no se salga
        burst.position.set(Math.max(x, 2.2), 0, 0)
        logo.scale.setScalar(LOGO_SCALE)
      } else {
        // Vertical: ahi el texto ocupa el ancho entero, asi que el emblema
        // baja al tercio inferior y encoge. Centrado se comia el titular.
        burst.position.set(0, -4.4, 0)
        logo.scale.setScalar(LOGO_SCALE * 0.72)
      }
    }
    frameBurst()
    // La medida depende de General Sans: si aun no ha cargado, el titular
    // ocupa otro ancho y el encuadre sale corrido.
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(frameBurst)

    /* El destello pasa a ser el resplandor que envuelve al emblema, no la
     * pieza central: se queda detras y se dibuja antes. */
    core.renderOrder = 2

    /* ── Campo de estrellas ─────────────────────────────────────────────
     * Una cascara esferica lejana, detras del estallido. */
    const starMat = new THREE.ShaderMaterial({
        transparent: true,
        blending: THREE.AdditiveBlending,
        depthWrite: false,
        depthTest: false,
        uniforms: {
        uTime: { value: 0 },
        uColor: { value: hexToVec3(CONFIG.starColor) },
        uTwinkle: { value: CONFIG.starTwinkle },
        uRes: { value: new THREE.Vector2(W * dpr, H * dpr) },
        },
        vertexShader: `
    attribute float size; attribute float seed; uniform float uTime, uTwinkle; uniform vec2 uRes; varying float vA;
    void main(){
    vec4 mv = modelViewMatrix * vec4(position, 1.0);
    vA = 0.4 + 0.6 * (0.5 + 0.5 * sin(uTime * uTwinkle + seed * 60.0));
    gl_PointSize = max(1.0, size * uRes.y / 900.0 / -mv.z);
    gl_Position = projectionMatrix * mv;
    }
    `,
        fragmentShader: `
    uniform vec3 uColor; varying float vA;
    void main(){ vec2 p = gl_PointCoord - 0.5; float l = length(p); if (l > 0.5) discard;
    float tex = smoothstep(0.5, 0.0, l); gl_FragColor = vec4(uColor * tex, tex * vA * 0.7); }
    `,
    })

    const stars = (() => {
        const N = Math.round(CONFIG.starCount)
        const positions = new Float32Array(N * 3), sizes = new Float32Array(N), seeds = new Float32Array(N)
        for (let i = 0; i < N; i++) {
        const u = Math.random() * 2 - 1, th = Math.random() * Math.PI * 2, rr = Math.sqrt(1 - u * u)
        const R = 60 + Math.random() * 30
        positions[i * 3] = rr * Math.cos(th) * R
        positions[i * 3 + 1] = rr * Math.sin(th) * R
        positions[i * 3 + 2] = -Math.abs(u * R) - 20      // detras de la singularidad
        sizes[i] = CONFIG.starSize * (0.5 + Math.random())
        seeds[i] = Math.random()
        }
        const g = new THREE.BufferGeometry()
        g.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3))
        g.setAttribute('size', new THREE.Float32BufferAttribute(sizes, 1))
        g.setAttribute('seed', new THREE.Float32BufferAttribute(seeds, 1))
        const p = new THREE.Points(g, starMat)
        p.frustumCulled = false
        p.layers.set(LAYERS.ENTIRE_SCENE)
        return p
    })()
    stars.onBeforeRender = () => { starMat.uniforms.uTime.value = performance.now() / 1000 }
    scene.add(stars)

    /* ── Motas de ambiente ──────────────────────────────────────────────
     * Sembradas en un cubo unidad, expandidas y deformadas en el shader, y
     * ancladas a la camara: derivan por delante del espectador. */
    const atmoMat = new THREE.ShaderMaterial({
        transparent: true,
        blending: THREE.AdditiveBlending,
        depthWrite: false,
        depthTest: false,
        uniforms: {
        uTime: { value: 0 },
        uColor: { value: hexToVec3(CONFIG.atmoColor) },
        uRes: { value: new THREE.Vector2(W * dpr, H * dpr) },
        },
        vertexShader: `
    attribute float size; attribute float seed; uniform float uTime; uniform vec2 uRes;
    varying float vA;
    vec3 warp(vec3 p, float t){ float c=0.9,a=1.9,b=0.02,s=0.05; p*=2.;
    p.x+=c*sin(s*t+a*p.y)+t*b; p.y+=c*cos(s*t+a*p.x); p.y+=c*sin(s*t+a*p.z)+t*b;
    p.z+=c*cos(s*t+a*p.y); p.z+=c*sin(s*t+a*p.x)+t*b; p.x+=c*cos(s*t+a*p.z);
    return cos(p+vec3(1,2,4)); }
    void main(){
    vec3 v = position*4.0 + warp(position, uTime)*1.2;
    vec4 mv = modelViewMatrix * vec4(v, 1.0);
    float r = length(v); float farF = 1.0 - smoothstep(5.0, 6.5, r); float nearF = smoothstep(0.0, 0.5, -mv.z);
    vA = farF * nearF;
    gl_PointSize = size * uRes.y / 900.0 / -mv.z; gl_PointSize = max(gl_PointSize, 1.0);
    gl_Position = projectionMatrix * mv;
    }
    `,
        fragmentShader: `
    uniform vec3 uColor; varying float vA;
    void main(){ vec2 p = gl_PointCoord - 0.5; float l = length(p); if (l > 0.5) discard;
    float tex = smoothstep(0.5, 0.0, l); gl_FragColor = vec4(uColor * tex, tex * vA * 0.4); }
    `,
    })

    const atmo = (() => {
        const N = Math.round(CONFIG.atmoCount)
        const positions = new Float32Array(N * 3), sizes = new Float32Array(N), seeds = new Float32Array(N)
        for (let i = 0; i < N; i++) {
        positions[i * 3] = 2 * Math.random() - 1; positions[i * 3 + 1] = 2 * Math.random() - 1; positions[i * 3 + 2] = 2 * Math.random() - 1
        sizes[i] = CONFIG.atmoSize * (0.4 + Math.random()); seeds[i] = Math.random()
        }
        const g = new THREE.BufferGeometry()
        g.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3))
        g.setAttribute('size', new THREE.Float32BufferAttribute(sizes, 1))
        g.setAttribute('seed', new THREE.Float32BufferAttribute(seeds, 1))
        const p = new THREE.Points(g, atmoMat)
        p.frustumCulled = false
        p.layers.set(LAYERS.ENTIRE_SCENE)
        return p
    })()
    scene.add(atmo)

    /* ── El pase compuesto final ────────────────────────────────────────
     * Fondo radial oscuro + resplandor central tenue + llamas azules en las
     * esquinas, con los render targets de torus y bloom por encima. */
    const FinalPass = {
        uniforms: {
        iTime: { value: 0 },
        tDiffuse: { value: null },
        torusTexture: { value: null },
        bloomTexture: { value: null },
        haloTexture: { value: null },
        uBg: { value: hexToVec3(CONFIG.bgColor) },
        uBg2: { value: hexToVec3(CONFIG.bgColor2) },
        uCoreBg: { value: hexToVec3(CONFIG.coreBg) },
        uFlameA: { value: hexToVec3(CONFIG.flameColor) },
        uFlameB: { value: hexToVec3(CONFIG.flameColor2) },
        uFlameAmt: { value: CONFIG.flameAmt },
        },
        vertexShader: `
    varying vec2 vUv; void main(){ vUv = uv; gl_Position = vec4(position, 1.0); }
    `,
        fragmentShader: `
    uniform float iTime; uniform sampler2D tDiffuse; uniform sampler2D bloomTexture; uniform sampler2D torusTexture; uniform sampler2D haloTexture;
    uniform vec3 uBg; uniform vec3 uBg2; uniform vec3 uCoreBg; uniform vec3 uFlameA; uniform vec3 uFlameB; uniform float uFlameAmt;
    varying vec2 vUv;
    vec3 warp3d(vec3 pos, float t){ float curv=.8,a=1.9,b=0.7; pos*=2.;
    pos.x+=curv*sin(t+a*pos.y)+t*b; pos.y+=curv*cos(t+a*pos.x);
    pos.y+=curv*sin(t+a*pos.z)+t*b; pos.z+=curv*cos(t+a*pos.y);
    pos.z+=curv*sin(t+a*pos.x)+t*b; pos.x+=curv*cos(t+a*pos.z);
    return 0.5+0.5*cos(pos.xyz+vec3(1,2,4)); }
    void main(){
    vec2 uv = 2.*vUv - 1.;
    vec3 w = pow(warp3d(vec3(uv.x, sin(uv.y), uv.y), iTime*1.5), vec3(1.5));
    vec3 flame = 1.5*uFlameA*w.x; flame*=w.y; flame += uFlameB*w.z;
    flame *= smoothstep(0.25, 1., abs(uv.y));
    float md = smoothstep(-0.7, 1., -uv.y*uv.x); flame *= md*md;
    // dark radial background + faint central glow toward the core
    float r = length(uv);
    vec3 bg = mix(uBg2, uBg, vUv.y) * (1.0 - 0.30 * r);
    bg += uCoreBg * exp(-r*r*2.2) * 0.6;
    vec3 halo = texture2D(haloTexture, vUv).xyz;
    gl_FragColor = vec4(bg + flame*uFlameAmt + texture2D(bloomTexture, vUv).xyz + texture2D(torusTexture, vUv).xyz + texture2D(tDiffuse, vUv).xyz + halo, 1.);
    }
    `,
    }

    /* ── Los tres composers ─────────────────────────────────────────── */
    const renderScene = new RenderPass(scene, camera)

    const torusComposer = new EffectComposer(renderer)
    torusComposer.renderToScreen = false
    torusComposer.addPass(renderScene)
    torusComposer.addPass(new ShaderPass(GammaCorrectionShader))
    torusComposer.addPass(new UnrealBloomPass(new THREE.Vector2(W, H), 0.22, 0.2, 0))
    torusComposer.addPass(new ShaderPass(CopyShader))

    const bloomComposer = new EffectComposer(renderer)
    bloomComposer.renderToScreen = false
    bloomComposer.addPass(renderScene)
    bloomComposer.addPass(new UnrealBloomPass(new THREE.Vector2(W, H), 0.4, 0.6, 0))
    bloomComposer.addPass(new ShaderPass(GammaCorrectionShader))

    const finalPass = new ShaderPass(FinalPass)
    const finalComposer = new EffectComposer(renderer)
    finalComposer.addPass(renderScene)
    finalComposer.addPass(finalPass)

    finalPass.uniforms.bloomTexture.value = bloomComposer.renderTarget1.texture
    finalPass.uniforms.torusTexture.value = torusComposer.renderTarget1.texture

    atmo.onBeforeRender = () => {
        atmoMat.uniforms.uTime.value = (performance.now() / 1000) * CONFIG.atmoSpeed * 8.0
        atmo.position.copy(camera.position)
        finalPass.uniforms.iTime.value = performance.now() / 1000
    }

    /* ── Puntero ────────────────────────────────────────────────────── */
    const target = { x: 0, y: 0 }
    const mouse = { x: 0, y: 0 }
    addEventListener('mousemove', (e) => {
        target.x = e.clientX / window.innerWidth * 2 - 1
        target.y = -(e.clientY / window.innerHeight * 2 - 1)
    })

    /* ── Resize ─────────────────────────────────────────────────────── */
    addEventListener('resize', () => {
        const s = sizeOf()
        W = s.w; H = s.h; dpr = window.devicePixelRatio
        renderer.setPixelRatio(dpr)
        renderer.setSize(W, H, false)
        camera.aspect = W / H
        camera.updateProjectionMatrix()
        for (const c of [torusComposer, bloomComposer, finalComposer]) {
        c.setPixelRatio(dpr)
        c.setSize(W, H)
        }
        filMat.uniforms.uRes.value.set(W * dpr, H * dpr)
        starMat.uniforms.uRes.value.set(W * dpr, H * dpr)
        atmoMat.uniforms.uRes.value.set(W * dpr, H * dpr)
        frameBurst()
    })

    /* ── ADAPTACION: pausa fuera de pantalla ────────────────────────────
     * El original es una pagina de una sola pantalla y siempre esta a la
     * vista. Aqui hay 7 secciones debajo, y dejar tres composers y 360.000
     * puntos girando mientras alguien lee los planes es tirar la bateria.
     * El bucle sigue vivo pero no dibuja cuando el hero sale de cuadro. */
    let visible = true
    new IntersectionObserver(
        (entries) => { visible = entries[0].isIntersecting },
        { threshold: 0 }
    ).observe(canvas)

    /* ── Bucle ──────────────────────────────────────────────────────── */
    const appearStart = performance.now()
    let prevT = performance.now() / 1000
    let spin = 0

    function animate() {
        requestAnimationFrame(animate)
        if (!visible) return

        const t = performance.now() / 1000
        const dt = Math.min(0.05, t - prevT)
        prevT = t

        filMat.uniforms.iTime.value = t

        mouse.x = Lerp(mouse.x, target.x, 0.06)
        mouse.y = Lerp(mouse.y, target.y, 0.06)
        const m = mouse

        // Entrada, una sola vez: rampa de ~1.8s tras 0.3s de espera.
        const el = performance.now() - appearStart
        const a = clamp((el - 300) / 1800, 0, 1) * CONFIG.opacity
        filMat.uniforms.uAlpha.value = a
        coreMat.uniforms.uAlpha.value = a
        logoMat.opacity = a

        // Giro lento automatico.
        spin += dt * CONFIG.autoSpin
        burst.rotation.z = spin

        // Inclinacion hacia el cursor.
        burst.rotation.x = Lerp(burst.rotation.x, -m.y * CONFIG.tilt, 0.05)
        burst.rotation.y = Lerp(burst.rotation.y, m.x * CONFIG.tilt, 0.05)

        // El destello siempre de cara a la camara.
        core.quaternion.copy(camera.quaternion)

        // El emblema se mantiene derecho contra el giro del grupo y bascula
        // despacio sobre su eje Y: es lo que deja ver el grosor de la
        // extrusion y el brillo recorriendole el bisel.
        logo.rotation.z = -spin
        logo.rotation.y = Math.sin(t * 0.45) * 0.55

        // Travelling de paralaje de la camara.
        camera.position.x = Lerp(camera.position.x, m.x * CONFIG.parallax, 0.05)
        camera.position.y = Lerp(camera.position.y, m.y * CONFIG.parallax, 0.05)
        camera.lookAt(0, 0, 0)

        camera.layers.set(LAYERS.TORUS_SCENE)
        torusComposer.render()
        camera.layers.set(LAYERS.BLOOM_SCENE)
        bloomComposer.render()
        camera.layers.set(LAYERS.ENTIRE_SCENE)
        finalComposer.render()
    }
    animate()
    </script>

    <script>
    /* ── La cascada de entrada ──────────────────────────────────────────
     * Puerto 1:1 del <Rise> de la app: cada elemento [data-rise] sale
     * disparado desde y:14px / opacity:0 hasta el reposo, escalonado 45ms
     * por paso, sobre un muelle de react-spring { tension: 220, friction:
     * 26 } — integrado aqui en vez de interpolado, para que el rebote sea
     * el de verdad y no una imitacion con bezier.
     *
     * Repetir: tecla R. */
    (() => {
        const STEP_MS = 45;
        const FROM_Y = 14;
        const TENSION = 220;
        const FRICTION = 26;
        const MASS = 1;
        const PRECISION = 0.01;
        const SUBSTEP = 1 / 240;

        const root = document.documentElement;
        root.classList.add("lk-js");

        const nodes = Array.from(document.querySelectorAll("[data-rise]"));
        const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        let generation = 0;

        const rest = (el) => {
        el.style.transform = "";
        el.style.opacity = "1";
        el.style.willChange = "";
        };

        const rise = (el, delayMs, gen) => {
        el.style.opacity = "0";
        el.style.transform = "translate3d(0," + FROM_Y + "px,0)";
        el.style.willChange = "transform, opacity";

        if (reduced) {
            window.setTimeout(() => { if (gen === generation) rest(el); }, delayMs);
            return;
        }

        let x = FROM_Y;
        let v = 0;
        let last = null;

        const frame = (now) => {
            if (gen !== generation) return;
            if (last === null) last = now;

            // Acotamos el paso para que una pestana en segundo plano no
            // haga explotar el integrador.
            const dt = Math.min((now - last) / 1000, 0.064);
            last = now;

            const steps = Math.max(1, Math.ceil(dt / SUBSTEP));
            const h = dt / steps;
            for (let i = 0; i < steps; i++) {
            const a = (-TENSION * x - FRICTION * v) / MASS;
            v += a * h;
            x += v * h;
            }

            // La opacidad monta la misma trayectoria normalizada que la y —
            // una sola forma de muelle, dos propiedades, exactamente como lo
            // resuelve react-spring.
            const p = 1 - x / FROM_Y;
            el.style.transform = "translate3d(0," + x.toFixed(3) + "px,0)";
            el.style.opacity = String(Math.max(0, Math.min(1, p)));

            if (Math.abs(x) < PRECISION && Math.abs(v) < PRECISION) return rest(el);
            requestAnimationFrame(frame);
        };

        // El escalonado es un temporizador, no una cuenta atras con rAF:
        // comparar una marca de tiempo de rAF contra un plazo de
        // performance.now() no dispara nunca bajo un reloj virtual, y ademas
        // quema un fotograma por elemento y tick.
        window.setTimeout(() => {
            if (gen === generation) requestAnimationFrame(frame);
        }, delayMs);
        };

        const play = () => {
        const gen = ++generation;
        nodes.forEach((el) => {
            const order = Number(el.dataset.rise) || 0;
            rise(el, order * STEP_MS, gen);
        });
        };

        play();

        addEventListener("keydown", (e) => {
        // ADAPTACION: esta pagina tiene un formulario de contacto, asi que
        // la R de repetir no puede dispararse mientras se escribe en un campo.
        if (e.target && e.target.closest("input, textarea, select")) return;
        if (e.key === "r" || e.key === "R") play();
        });
    })();

    /* ── El nav fijo de la landing ──────────────────────────────────────
     * La composicion trae su propio cromo, asi que el nav original se
     * esconde mientras el hero llena la pantalla y entra al pasarlo. */
    (() => {
        const hero = document.getElementById("inicio");
        if (!hero) return;
        const sync = () => {
        const past = window.scrollY > hero.offsetHeight - 90;
        document.body.classList.toggle("hero-visible", !past);
        };
        sync();
        addEventListener("scroll", sync, { passive: true });
        addEventListener("resize", sync);
    })();
    </script>

        <script>
    /* ══════════════════════════════════════════════════════════════════
     * SPOTLIGHT FRAMES — puerto a JS plano
     * ══════════════════════════════════════════════════════════════════
     * Misma geometria que el componente de Originkit: las lamas se colocan
     * a su tamano de autor y despues la tira ENTERA se escala por un unico
     * factor si no cabe en el marco. Escalar en vez de descartar lamas
     * mantiene la disposicion identica a cualquier ancho, que es lo que el
     * proyecto original resolvia con un breakpoint duro.
     *
     * Para cambiar el reparto, toca AUTHORED: son los `track.collapsedWidth`,
     * `expandedWidth` y `gap` del componente. */
    (() => {
        const strip = document.getElementById('sfStrip');
        if (!strip) return;

        const track = strip.querySelector('.sf-track');
        const slats = Array.from(track.querySelectorAll('.sf-slat'));
        const selector = track.querySelector('.sf-selector');
        const COUNT = slats.length;
        if (!COUNT) return;

        const AUTHORED = { collapsed: 138, expanded: 560, gap: 2 };
        const START_INDEX = 0;
        const STAGGER_MS = 40;

        let focused = Math.min(Math.max(START_INDEX, 0), COUNT - 1);

        /* ---- geometria --------------------------------------------------- */
        function layout(animate) {
        if (!animate) track.classList.add('sf-noanim');

        const frameW = strip.clientWidth;
        const natural =
            (COUNT - 1) * (AUTHORED.collapsed + AUTHORED.gap) + AUTHORED.expanded;
        const scale = natural > frameW && frameW > 0 ? frameW / natural : 1;

        const c = AUTHORED.collapsed * scale;
        const e = AUTHORED.expanded * scale;
        const g = AUTHORED.gap * scale;

        // El contenido abierto se maqueta a este ancho y la lama lo recorta,
        // de modo que el texto no se recompone durante la apertura.
        track.style.setProperty('--sf-open-w', e + 'px');

        let left = (frameW - natural * scale) / 2;
        slats.forEach((el, i) => {
            const w = i === focused ? e : c;
            el.style.left = left + 'px';
            el.style.width = w + 'px';
            if (i === focused && selector) {
            selector.style.left = left + 'px';
            selector.style.width = w + 'px';
            }
            left += w + g;
        });

        if (!animate) {
            // Forzamos el reflow antes de devolver las transiciones: si no,
            // el navegador agrupa ambos cambios y la tira se desliza igual.
            void track.offsetWidth;
            track.classList.remove('sf-noanim');
        }
        }

        function focusSlat(i) {
        if (i === focused) return;
        focused = i;
        slats.forEach((el, k) => el.classList.toggle('is-open', k === focused));
        layout(true);
        }

        /* ---- apertura: hover con raton, toque en pantalla tactil --------- */
        const fine = window.matchMedia('(hover: hover) and (pointer: fine)');
        let opensOnHover = fine.matches;
        const readPointer = () => { opensOnHover = fine.matches; };
        fine.addEventListener ? fine.addEventListener('change', readPointer)
                            : fine.addListener(readPointer);

        slats.forEach((el, i) => {
        el.addEventListener('pointerenter', (ev) => {
            if (opensOnHover && ev.pointerType !== 'touch') focusSlat(i);
        });
        el.addEventListener('click', () => focusSlat(i));
        el.addEventListener('focus', () => focusSlat(i));
        el.addEventListener('keydown', (ev) => {
            const step = ev.key === 'ArrowRight' ? 1 : ev.key === 'ArrowLeft' ? -1 : 0;
            if (!step) return;
            ev.preventDefault();
            const next = Math.min(Math.max(i + step, 0), COUNT - 1);
            focusSlat(next);
            slats[next].focus();
        });
        });

        /* ---- arranque ---------------------------------------------------- */
        slats.forEach((el, i) => el.classList.toggle('is-open', i === focused));
        layout(false);

        let raf = 0;
        addEventListener('resize', () => {
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => layout(false));
        });

        /* La entrada se dispara al entrar en pantalla, escalonada por indice,
         * como el `entrance.stagger` del componente. */
        track.classList.add('sf-enter');
        slats.forEach((el, i) => {
        el.querySelector('.sf-inner').style.transitionDelay = (i * STAGGER_MS) + 'ms';
        });
        new IntersectionObserver((entries, obs) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            track.classList.add('sf-in');
            obs.disconnect();
        });
        }, { threshold: .18 }).observe(track);
    })();
    </script>
        <script>
    /* ══════════════════════════════════════════════════════════════════
     * GOOEY NAV — puerto a JS plano
     * ══════════════════════════════════════════════════════════════════
     * Mismas matematicas que el componente de React Bits: las particulas se
     * reparten en circulo con un poco de ruido, salen de --start-x/y y
     * caen en --end-x/y, cada una con su duracion, escala, giro y color.
     * Los props del componente viven en CONF. */
    (() => {
        const container = document.querySelector('.gooey-nav-container');
        if (!container) return;

        const list = container.querySelector('ul');
        const items = Array.from(list.querySelectorAll('li'));
        const filterEl = container.querySelector('.effect.filter');
        const textEl = container.querySelector('.effect.text');
        if (!items.length || !filterEl || !textEl) return;

        const CONF = {
        animationTime: 600,
        particleCount: 15,
        particleDistances: [90, 10],
        particleR: 100,
        timeVariance: 300,
        colors: [1, 2, 3, 1, 2, 3, 1, 4],
        initialActiveIndex: 0,
        };

        let activeIndex = CONF.initialActiveIndex;

        const noise = (n = 1) => n / 2 - Math.random() * n;

        const getXY = (distance, pointIndex, totalPoints) => {
        const angle = ((360 + noise(8)) / totalPoints) * pointIndex * (Math.PI / 180);
        return [distance * Math.cos(angle), distance * Math.sin(angle)];
        };

        const createParticle = (i, t, d, r) => {
        const rotate = noise(r / 10);
        return {
            start: getXY(d[0], CONF.particleCount - i, CONF.particleCount),
            end: getXY(d[1] + noise(7), CONF.particleCount - i, CONF.particleCount),
            time: t,
            scale: 1 + noise(0.2),
            color: CONF.colors[Math.floor(Math.random() * CONF.colors.length)],
            rotate: rotate > 0 ? (rotate + r / 20) * 10 : (rotate - r / 20) * 10,
        };
        };

        const makeParticles = (element) => {
        const d = CONF.particleDistances;
        const r = CONF.particleR;
        const bubbleTime = CONF.animationTime * 2 + CONF.timeVariance;
        element.style.setProperty('--time', bubbleTime + 'ms');

        for (let i = 0; i < CONF.particleCount; i++) {
            const t = CONF.animationTime * 2 + noise(CONF.timeVariance * 2);
            const p = createParticle(i, t, d, r);
            element.classList.remove('active');

            setTimeout(() => {
            const particle = document.createElement('span');
            const point = document.createElement('span');
            particle.classList.add('particle');
            particle.style.setProperty('--start-x', p.start[0] + 'px');
            particle.style.setProperty('--start-y', p.start[1] + 'px');
            particle.style.setProperty('--end-x', p.end[0] + 'px');
            particle.style.setProperty('--end-y', p.end[1] + 'px');
            particle.style.setProperty('--time', p.time + 'ms');
            particle.style.setProperty('--scale', String(p.scale));
            particle.style.setProperty('--color', 'var(--color-' + p.color + ', white)');
            particle.style.setProperty('--rotate', p.rotate + 'deg');

            point.classList.add('point');
            particle.appendChild(point);
            element.appendChild(particle);
            requestAnimationFrame(() => element.classList.add('active'));
            setTimeout(() => {
                try { element.removeChild(particle); } catch (e) { /* ya retirada */ }
            }, t);
            }, 30);
        }
        };

        /* La pildora y el texto del efecto se colocan sobre el item activo.
         * Se mide en coordenadas relativas al contenedor, asi que da igual que
         * la barra este desplazada por su propia transformacion mientras el
         * hero ocupa la pantalla. */
        const updateEffectPosition = (element) => {
        const containerRect = container.getBoundingClientRect();
        const pos = element.getBoundingClientRect();
        const styles = {
            left: (pos.x - containerRect.x) + 'px',
            top: (pos.y - containerRect.y) + 'px',
            width: pos.width + 'px',
            height: pos.height + 'px',
        };
        Object.assign(filterEl.style, styles);
        Object.assign(textEl.style, styles);
        textEl.innerText = element.innerText;
        };

        const activate = (index) => {
        const li = items[index];
        if (!li || index === activeIndex) return;

        activeIndex = index;
        items.forEach((el, k) => el.classList.toggle('active', k === index));
        updateEffectPosition(li);

        filterEl.querySelectorAll('.particle').forEach((p) => filterEl.removeChild(p));

        textEl.classList.remove('active');
        void textEl.offsetWidth;   // reinicia la animacion del texto
        textEl.classList.add('active');

        makeParticles(filterEl);
        };

        items.forEach((li, index) => {
        // Sin preventDefault: el enlace sigue navegando a su seccion y el
        // scroll suave de la pagina se encarga del resto.
        li.addEventListener('click', () => activate(index));
        const a = li.querySelector('a');
        if (a) a.addEventListener('keydown', (ev) => {
            if (ev.key === 'Enter' || ev.key === ' ') { ev.preventDefault(); a.click(); }
        });
        });

        items.forEach((el, k) => el.classList.toggle('active', k === activeIndex));
        const settle = () => {
        const li = items[activeIndex];
        if (!li) return;
        updateEffectPosition(li);
        textEl.classList.add('active');
        };
        settle();
        // La pildora se mide con la tipografia ya cargada: con la de respaldo
        // los items tienen otro ancho y quedaba descuadrada.
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(settle);

        new ResizeObserver(() => {
        const li = items[activeIndex];
        if (li) updateEffectPosition(li);
        }).observe(container);
    })();
    </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js"></script>
    <script>
    /* ══════════════════════════════════════════════════════════════════
     * BOUNCE CARDS — puerto a JS plano
     * ══════════════════════════════════════════════════════════════════
     * Las funciones de transformacion son las del componente, tal cual:
     * getNoRotationTransform endereza la tocada, getPushedTransform suma el
     * desplazamiento a las demas, y el retardo crece con la distancia al
     * indice tocado. Como aqui las tarjetas van en una sola hilera —igual
     * que en el original— el orden del indice es el orden en pantalla y el
     * empujon se reparte por indice sin mas.
     *
     * Los props del componente viven en CONF. */
    (() => {
        const grid = document.querySelector('.bots-grid');
        if (!grid) return;
        const cards = Array.from(grid.querySelectorAll('.bc-card'));
        if (!cards.length) return;

        // Sin GSAP no hay easing elastico que valga: mejor dejar las tarjetas
        // quietas y visibles que a medio animar.
        if (!window.gsap) {
        console.warn('BounceCards: GSAP no disponible, se omite la animacion.');
        return;
        }
        const gsap = window.gsap;

        const CONF = {
        animationDelay: 0.15,
        animationStagger: 0.06,
        easeType: 'elastic.out(1, 0.8)',
        enableHover: true,
        pushOffset: 190,      // con 8 tarjetas el solape es de 186: esto lo deshace
        naturalWidth: 1300,   // 1258 de abanico + aire para el giro
        fanMinWidth: 1041,    // por debajo, columna en vez de abanico
        };

        const TRANSFORMS = cards.map((el) => el.dataset.rest || 'none');

        /* ---- utilidades del componente, verbatim ---------------------- */
        const getNoRotationTransform = (t) => {
        if (/rotate\([\s\S]*?\)/.test(t)) return t.replace(/rotate\([\s\S]*?\)/, 'rotate(0deg)');
        if (t === 'none') return 'rotate(0deg)';
        return t + ' rotate(0deg)';
        };

        const getPushedTransform = (base, offsetX) => {
        const re = /translate\(([-0-9.]+)px\)/;
        const m = base.match(re);
        if (m) return base.replace(re, 'translate(' + (parseFloat(m[1]) + offsetX) + 'px)');
        return base === 'none' ? 'translate(' + offsetX + 'px)'
                                : base + ' translate(' + offsetX + 'px)';
        };

        const fanned = () => window.innerWidth >= CONF.fanMinWidth;

        /* La tira se escala para caber, como el abanico de ancho fijo del
         * original metido en un marco que no siempre da esa medida. */
        const fit = () => {
        if (!fanned()) { grid.style.removeProperty('--bc-scale'); return; }
        const w = grid.parentElement.clientWidth;
        grid.style.setProperty('--bc-scale', String(Math.min(1, w / CONF.naturalWidth)));
        };

        /* ---- hover ---------------------------------------------------- */
        let entranceDone = false;

        const pushSiblings = (hoveredIdx) => {
        // Durante la entrada el hover escribiria `transform` encima de la
        // escala en vuelo y la tarjeta pegaria un salto a tamano completo.
        if (!CONF.enableHover || !entranceDone || !fanned()) return;

        cards.forEach((el, i) => {
            gsap.killTweensOf(el);
            const base = TRANSFORMS[i] || 'none';

            if (i === hoveredIdx) {
            el.classList.add('bc-hot');
            gsap.to(el, {
                transform: getNoRotationTransform(base),
                duration: 0.4, ease: 'back.out(1.4)', overwrite: 'auto',
            });
            } else {
            el.classList.remove('bc-hot');
            const offsetX = i < hoveredIdx ? -CONF.pushOffset : CONF.pushOffset;
            gsap.to(el, {
                transform: getPushedTransform(base, offsetX),
                duration: 0.4, ease: 'back.out(1.4)',
                delay: Math.abs(hoveredIdx - i) * 0.05, overwrite: 'auto',
            });
            }
        });
        };

        const resetSiblings = () => {
        if (!CONF.enableHover || !entranceDone || !fanned()) return;
        cards.forEach((el, i) => {
            gsap.killTweensOf(el);
            el.classList.remove('bc-hot');
            gsap.to(el, {
            transform: TRANSFORMS[i] || 'none',
            duration: 0.4, ease: 'back.out(1.4)', overwrite: 'auto',
            });
        });
        };

        cards.forEach((el, i) => {
        el.addEventListener('mouseenter', () => pushSiblings(i));
        el.addEventListener('mouseleave', resetSiblings);
        });

        /* ---- entrada --------------------------------------------------
         * El componente arranca con un `delay` fijo porque vive en pantalla
         * desde el principio. Aqui la seccion esta muy por debajo del hero,
         * asi que el rebote espera a que entre en cuadro. */
        const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

        /* Encogemos aqui, no en la hoja de estilos: el transform de reposo va
         * en linea en cada tarjeta y le ganaria a cualquier regla CSS. Se
         * ejecuta al parsear el final del <body>, con la seccion muy por
         * debajo del pliegue, asi que nadie llega a verlas a tamano completo.
         * Y como esto solo corre si GSAP existe, sin GSAP se quedan visibles.
         *
         * Lo hace gsap.set y no un `scale(0)` escrito a mano dentro del
         * transform en linea: una matriz con escala cero es todo ceros, y de
         * ahi ya no se puede recuperar el angulo. GSAP leia rotacion 0 y las
         * tarjetas se quedaban rectas. Asi parsea primero la inclinacion y
         * despues aplica la escala sobre su propia cache. */
        if (!reduced) gsap.set(cards, { scale: 0 });

        const play = () => {
        if (reduced) { entranceDone = true; return; }
        gsap.fromTo(cards,
            { scale: 0 },
            {
            scale: 1,
            stagger: CONF.animationStagger,
            ease: CONF.easeType,
            delay: CONF.animationDelay,
            onComplete: () => { entranceDone = true; },
            }
        );
        };

        fit();
        addEventListener('resize', fit);

        new IntersectionObserver((entries, obs) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            play();
            obs.disconnect();
        });
        }, { threshold: 0.15 }).observe(grid);
    })();
    </script>
        <script>
    /* ══════════════════════════════════════════════════════════════════
     * STACK — puerto a JS plano
     * ══════════════════════════════════════════════════════════════════
     * La logica del mazo es la del componente: sendToBack saca la tarjeta de
     * su sitio y la mete al principio del array, y cada una se coloca segun
     * su indice con
     *      rotateZ = (n - indice - 1) * 4 + giro aleatorio
     *      escala  = 1 + indice * 0.06 - n * 0.06
     * y el origen de transformacion en 90% 90%.
     *
     * El original mueve esos valores con muelles de Framer Motion. Aqui van
     * con un integrador propio de los mismos parametros (stiffness 260,
     * damping 20), no con una curva bezier que los imite: el rebote es el de
     * verdad. El arrastre replica dragElastic 0.6 y el volteo 3D mapeado
     * desde el desplazamiento, con el umbral de sensibilidad sobre el
     * recorrido REAL del puntero, como hace info.offset.
     *
     * Los props del componente viven en CONF. */
    (() => {
        const stackEl = document.getElementById('cliStack');
        if (!stackEl) return;
        const rotates = Array.from(stackEl.querySelectorAll('.st-rotate'));
        const n = rotates.length;
        if (n < 2) return;
        const dots = Array.from(document.querySelectorAll('#cliDots .st-dot'));

        const CONF = {
        randomRotation: true,
        sensitivity: 180,
        sendToBackOnClick: true,
        animationConfig: { stiffness: 260, damping: 20 },
        autoplay: true,
        autoplayDelay: 10000,   // 3000 en el componente; 10s a peticion
        pauseOnHover: false,
        };

        const K = CONF.animationConfig.stiffness;
        const C = CONF.animationConfig.damping;
        const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));

        /* order[0] = fondo del mazo, order[n-1] = la de arriba.
         * El componente reordena el array y deja que el DOM pinte en ese
         * orden; aqui el DOM no se toca y manda el z-index, que da el mismo
         * resultado sin mover nodos. */
        let order = rotates.map((_, i) => i);

        const items = rotates.map((el) => ({
        el,
        card: el.querySelector('.st-card'),
        rnd: 0,
        rot: { val: 0, v: 0, t: 0 },
        scl: { val: 1, v: 0, t: 1 },
        x: { val: 0, v: 0, t: 0 },
        y: { val: 0, v: 0, t: 0 },
        dragging: false,
        }));

        const apply = (it) => {
        // useTransform(y, [-100,100], [60,-60]) y (x, [-100,100], [-60,60])
        const rx = clamp(-it.y.val * 0.6, -60, 60);
        const ry = clamp(it.x.val * 0.6, -60, 60);
        it.el.style.transform =
            'translate3d(' + it.x.val.toFixed(2) + 'px,' + it.y.val.toFixed(2) + 'px,0)' +
            ' rotateX(' + rx.toFixed(2) + 'deg) rotateY(' + ry.toFixed(2) + 'deg)';
        it.card.style.transform =
            'rotateZ(' + it.rot.val.toFixed(3) + 'deg) scale(' + it.scl.val.toFixed(4) + ')';
        };

        const retarget = () => {
        order.forEach((id, idx) => {
            const it = items[id];
            it.rot.t = (n - idx - 1) * 4 + it.rnd;
            it.scl.t = 1 + idx * 0.06 - n * 0.06;
            it.el.style.zIndex = String(idx);
            it.card.classList.toggle('is-top', idx === n - 1);
        });
        dots.forEach((d, i) => d.classList.toggle('is-on', i === order[n - 1]));
        };

        // El componente recalcula el giro aleatorio en cada render, asi que
        // el mazo se recoloca entero en cada pase. Se conserva.
        const roll = () => {
        if (!CONF.randomRotation) return;
        items.forEach((it) => { it.rnd = Math.random() * 10 - 5; });
        };

        /* ---- muelle ---------------------------------------------------- */
        let raf = 0, last = 0;

        const tick = (now) => {
        const dt = Math.min((now - last) / 1000, 0.064);
        last = now;
        const steps = Math.max(1, Math.ceil(dt / (1 / 240)));
        const h = dt / steps;

        let moving = false;
        for (const it of items) {
            for (const s of [it.rot, it.scl, it.x, it.y]) {
            // Mientras se arrastra, x/y las escribe el puntero: el muelle
            // no debe tirar de ellas.
            if (it.dragging && (s === it.x || s === it.y)) continue;
            for (let i = 0; i < steps; i++) {
                const a = -K * (s.val - s.t) - C * s.v;
                s.v += a * h;
                s.val += s.v * h;
            }
            if (Math.abs(s.val - s.t) > 1e-3 || Math.abs(s.v) > 1e-3) moving = true;
            else { s.val = s.t; s.v = 0; }
            }
            apply(it);
        }
        raf = (moving || items.some((i) => i.dragging)) ? requestAnimationFrame(tick) : 0;
        };

        const start = () => { if (!raf) { last = performance.now(); raf = requestAnimationFrame(tick); } };

        const sendToBack = (id) => {
        const i = order.indexOf(id);
        if (i < 0) return;
        order.splice(i, 1);
        order.unshift(id);
        roll();
        retarget();
        start();
        };

        /* ---- autoplay --------------------------------------------------- */
        let paused = false, onScreen = true, timer = 0;
        const schedule = () => {
        clearInterval(timer);
        if (!CONF.autoplay) return;
        timer = setInterval(() => {
            if (paused || !onScreen) return;
            sendToBack(order[n - 1]);   // la de arriba, al fondo
        }, CONF.autoplayDelay);
        };

        if (CONF.pauseOnHover) {
        stackEl.addEventListener('mouseenter', () => { paused = true; });
        stackEl.addEventListener('mouseleave', () => { paused = false; });
        }

        /* Fuera de pantalla no pasa turno: asi al llegar a la seccion se ven
         * los 10 segundos enteros en vez de un mazo ya barajado. */
        new IntersectionObserver((entries) => {
        entries.forEach((e) => { onScreen = e.isIntersecting; });
        }, { threshold: 0.2 }).observe(stackEl);

        /* ---- arrastre ---------------------------------------------------- */
        let suppressClick = false;

        rotates.forEach((el, id) => {
        let sx = 0, sy = 0, rawX = 0, rawY = 0, moved = false, pid = null;

        el.addEventListener('pointerdown', (ev) => {
            pid = ev.pointerId;
            try { el.setPointerCapture(pid); } catch (e) { /* da igual */ }
            sx = ev.clientX; sy = ev.clientY;
            rawX = rawY = 0; moved = false;
            items[id].dragging = true;
            el.classList.add('is-drag');
            paused = true;
            start();
        });

        el.addEventListener('pointermove', (ev) => {
            if (pid === null) return;
            rawX = ev.clientX - sx;
            rawY = ev.clientY - sy;
            if (Math.abs(rawX) > 4 || Math.abs(rawY) > 4) moved = true;
            const it = items[id];
            it.x.val = rawX * 0.6;   // dragElastic
            it.y.val = rawY * 0.6;
            it.x.v = it.y.v = 0;
            apply(it);
        });

        const end = () => {
            if (pid === null) return;
            try { el.releasePointerCapture(pid); } catch (e) { /* da igual */ }
            pid = null;
            const it = items[id];
            it.dragging = false;
            it.x.t = 0; it.y.t = 0;
            el.classList.remove('is-drag');
            paused = false;
            // El umbral mira el recorrido real del puntero, no el elastico.
            if (Math.abs(rawX) > CONF.sensitivity || Math.abs(rawY) > CONF.sensitivity) sendToBack(id);
            else start();
            // Tras arrastrar llega un click: no debe contar como pulsacion.
            suppressClick = moved;
        };
        el.addEventListener('pointerup', end);
        el.addEventListener('pointercancel', end);

        el.addEventListener('click', () => {
            if (suppressClick) { suppressClick = false; return; }
            if (CONF.sendToBackOnClick) sendToBack(id);
        });
        });

        /* ---- arranque ---------------------------------------------------- */
        roll();
        retarget();
        // initial={false} en el original: el mazo aparece ya colocado.
        items.forEach((it) => {
        it.rot.val = it.rot.t; it.scl.val = it.scl.t;
        apply(it);
        });
        schedule();
    })();
    </script>
        <script>
    /* ══════════════════════════════════════════════════════════════════
     * FOLDER — puerto a JS plano
     * ══════════════════════════════════════════════════════════════════
     * La logica del componente es corta: un booleano `open` que se conmuta
     * al pulsar, y un iman por papel que sigue al puntero mientras esta
     * abierta. darkenColor va verbatim — es lo que saca el tono del dorso a
     * partir del color de la carpeta. */
    (() => {
        const folder = document.getElementById('planFolder');
        if (!folder) return;
        const wrap = folder.closest('.fld-wrap');
        const papers = Array.from(folder.querySelectorAll('.paper'));

        const CONF = {
        color: '#ffffff',      // #5227FF en el componente; blanca a peticion
        magnet: 0.15,          // el factor del iman del original
        };

        /* ---- verbatim del componente ----------------------------------- */
        const darkenColor = (hex, percent) => {
        let color = hex.startsWith('#') ? hex.slice(1) : hex;
        if (color.length === 3) color = color.split('').map((c) => c + c).join('');
        const num = parseInt(color.slice(0, 6), 16);
        let r = (num >> 16) & 0xff, g = (num >> 8) & 0xff, b = num & 0xff;
        r = Math.max(0, Math.min(255, Math.floor(r * (1 - percent))));
        g = Math.max(0, Math.min(255, Math.floor(g * (1 - percent))));
        b = Math.max(0, Math.min(255, Math.floor(b * (1 - percent))));
        return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1).toUpperCase();
        };

        folder.style.setProperty('--folder-color', CONF.color);
        folder.style.setProperty('--folder-back-color', darkenColor(CONF.color, 0.08));
        /* Los papeles del original son blancos porque la carpeta era de
         * color. Aqui la carpeta es blanca y dentro van planes con texto
         * claro, asi que se invierte: papeles oscuros, con el mismo escalon
         * de tono entre ellos que daba darkenColor. */
        folder.style.setProperty('--paper-1', '#141414');
        folder.style.setProperty('--paper-2', '#101010');
        folder.style.setProperty('--paper-3', '#0b0b0b');

        /* ---- abrir y cerrar -------------------------------------------- */
        let open = false;

        const clearMagnets = () => {
        papers.forEach((p) => {
            p.style.removeProperty('--magnet-x');
            p.style.removeProperty('--magnet-y');
        });
        };

        const toggle = () => {
        open = !open;
        folder.classList.toggle('open', open);
        wrap.classList.toggle('is-open', open);
        folder.setAttribute('aria-expanded', String(open));
        folder.setAttribute('aria-label', open ? 'Cerrar planes' : 'Abrir planes');
        if (!open) clearMagnets();   // el componente los resetea al cerrar
        };

        folder.addEventListener('click', toggle);
        folder.addEventListener('keydown', (ev) => {
        if (ev.key === 'Enter' || ev.key === ' ') { ev.preventDefault(); toggle(); }
        });

        /* Los papeles del original son decoracion; estos llevan el boton de
         * cada plan. Sin esto, pulsar "Comenzar Gratis" cerraria la carpeta
         * en vez de llevarte al formulario. */
        folder.querySelectorAll('.paper a, .paper button').forEach((el) => {
        el.addEventListener('click', (ev) => ev.stopPropagation());
        });

        /* ---- el iman ---------------------------------------------------- */
        papers.forEach((paper) => {
        paper.addEventListener('mousemove', (ev) => {
            if (!open) return;
            const r = paper.getBoundingClientRect();
            const cx = r.left + r.width / 2;
            const cy = r.top + r.height / 2;
            paper.style.setProperty('--magnet-x', ((ev.clientX - cx) * CONF.magnet).toFixed(1) + 'px');
            paper.style.setProperty('--magnet-y', ((ev.clientY - cy) * CONF.magnet).toFixed(1) + 'px');
        });
        paper.addEventListener('mouseleave', () => {
            paper.style.setProperty('--magnet-x', '0px');
            paper.style.setProperty('--magnet-y', '0px');
        });
        });
    })();
    </script>
        <script>
    /* ══════════════════════════════════════════════════════════════════
     * PROFILE CARD — puerto a JS plano
     * ══════════════════════════════════════════════════════════════════
     * El motor de inclinacion del componente, tal cual: una interpolacion
     * exponencial hacia la posicion del puntero con constante de tiempo tau
     *      k = 1 - exp(-dt / tau)
     * con tau 0.14 en marcha normal y 0.6 durante la entrada, que es lo que
     * hace que el primer barrido se sienta pesado y el seguimiento posterior
     * inmediato. clamp, round y adjust van verbatim, y las seis variables
     * que publica son las mismas.
     *
     * Lo unico que cambia es cuando arranca la entrada: el componente la
     * lanza al montarse porque su tarjeta esta en pantalla desde el
     * principio; aqui la seccion vive al final de la pagina, asi que espera
     * a entrar en cuadro. Si no, el barrido se lo perdia todo el mundo. */
    (() => {
        const wrap = document.getElementById('ctaCard');
        if (!wrap) return;
        const shell = wrap.querySelector('.pc-card-shell');
        const card = wrap.querySelector('.pc-card');
        if (!shell || !card) return;

        const CONF = {
        enableTilt: true,
        INITIAL_DURATION: 1200,
        INITIAL_X_OFFSET: 70,
        INITIAL_Y_OFFSET: 60,
        ENTER_TRANSITION_MS: 180,
        DEFAULT_TAU: 0.14,
        INITIAL_TAU: 0.6,
        };
        if (!CONF.enableTilt) return;
        if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        /* ---- verbatim del componente ----------------------------------- */
        const clamp = (v, min = 0, max = 100) => Math.min(Math.max(v, min), max);
        const round = (v, p = 3) => parseFloat(v.toFixed(p));
        const adjust = (v, fMin, fMax, tMin, tMax) =>
        round(tMin + ((tMax - tMin) * (v - fMin)) / (fMax - fMin));

        let rafId = null, running = false, lastTs = 0;
        let currentX = 0, currentY = 0, targetX = 0, targetY = 0;
        let initialUntil = 0;

        const setVarsFromXY = (x, y) => {
        const width = shell.clientWidth || 1;
        const height = shell.clientHeight || 1;
        const percentX = clamp((100 / width) * x);
        const percentY = clamp((100 / height) * y);
        const centerX = percentX - 50;
        const centerY = percentY - 50;

        wrap.style.setProperty('--pointer-x', percentX + '%');
        wrap.style.setProperty('--pointer-y', percentY + '%');
        wrap.style.setProperty('--background-x', adjust(percentX, 0, 100, 35, 65) + '%');
        wrap.style.setProperty('--background-y', adjust(percentY, 0, 100, 35, 65) + '%');
        wrap.style.setProperty('--pointer-from-center',
            String(clamp(Math.hypot(percentY - 50, percentX - 50) / 50, 0, 1)));
        wrap.style.setProperty('--pointer-from-top', String(percentY / 100));
        wrap.style.setProperty('--pointer-from-left', String(percentX / 100));
        wrap.style.setProperty('--rotate-x', round(-(centerX / 5)) + 'deg');
        wrap.style.setProperty('--rotate-y', round(centerY / 4) + 'deg');
        };

        const step = (ts) => {
        if (!running) return;
        if (lastTs === 0) lastTs = ts;
        const dt = (ts - lastTs) / 1000;
        lastTs = ts;

        const tau = ts < initialUntil ? CONF.INITIAL_TAU : CONF.DEFAULT_TAU;
        const k = 1 - Math.exp(-dt / tau);

        currentX += (targetX - currentX) * k;
        currentY += (targetY - currentY) * k;
        setVarsFromXY(currentX, currentY);

        const stillFar = Math.abs(targetX - currentX) > 0.05 || Math.abs(targetY - currentY) > 0.05;
        if (stillFar) {
            rafId = requestAnimationFrame(step);
        } else {
            // El original seguia girando mientras la pestana tuviera el foco,
            // aunque ya no hubiera nada que mover. Aqui se para: no hay
            // ningun estado que dependa de que el bucle siga vivo.
            running = false; lastTs = 0;
            if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
        }
        };

        const start = () => { if (running) return; running = true; lastTs = 0; rafId = requestAnimationFrame(step); };
        const setImmediate_ = (x, y) => { currentX = x; currentY = y; setVarsFromXY(x, y); };
        const setTarget = (x, y) => { targetX = x; targetY = y; start(); };
        const toCenter = () => setTarget(shell.clientWidth / 2, shell.clientHeight / 2);
        const beginInitial = (ms) => { initialUntil = performance.now() + ms; start(); };

        const getOffsets = (evt) => {
        const rect = shell.getBoundingClientRect();
        return { x: evt.clientX - rect.left, y: evt.clientY - rect.top };
        };

        /* ---- puntero ---------------------------------------------------- */
        let enterTimer = 0, leaveRaf = 0;

        shell.addEventListener('pointerenter', (ev) => {
        card.classList.add('active');
        wrap.classList.add('active');
        shell.classList.add('entering');
        clearTimeout(enterTimer);
        enterTimer = setTimeout(() => shell.classList.remove('entering'), CONF.ENTER_TRANSITION_MS);
        const { x, y } = getOffsets(ev);
        setTarget(x, y);
        });

        shell.addEventListener('pointermove', (ev) => {
        const { x, y } = getOffsets(ev);
        setTarget(x, y);
        });

        shell.addEventListener('pointerleave', () => {
        toCenter();
        // La clase `active` no se quita de golpe: se espera a que la
        // inclinacion haya vuelto al centro, o la tarjeta pegaria un salto.
        const checkSettle = () => {
            const settled = Math.hypot(targetX - currentX, targetY - currentY) < 0.6;
            if (settled) {
            card.classList.remove('active');
            wrap.classList.remove('active');
            leaveRaf = 0;
            } else {
            leaveRaf = requestAnimationFrame(checkSettle);
            }
        };
        if (leaveRaf) cancelAnimationFrame(leaveRaf);
        leaveRaf = requestAnimationFrame(checkSettle);
        });

        /* ---- entrada ---------------------------------------------------- */
        const intro = () => {
        const x = (shell.clientWidth || 0) - CONF.INITIAL_X_OFFSET;
        setImmediate_(x, CONF.INITIAL_Y_OFFSET);
        toCenter();
        beginInitial(CONF.INITIAL_DURATION);
        };

        setVarsFromXY(shell.clientWidth / 2, shell.clientHeight / 2);
        new IntersectionObserver((entries, obs) => {
        entries.forEach((e) => {
            if (!e.isIntersecting) return;
            intro();
            obs.disconnect();
        });
        }, { threshold: 0.35 }).observe(wrap);

        addEventListener('resize', () => setVarsFromXY(currentX, currentY));
    })();
    </script>
        <script>
    /* ══════════════════════════════════════════════════════════════════
     * TARGET CURSOR — puerto a JS plano
     * ══════════════════════════════════════════════════════════════════
     * Toda la mecanica del componente: el seguimiento con power3.out, el
     * giro continuo, el enganche de las cuatro escuadras al recuadro del
     * objetivo con su rampa de fuerza, el ticker que las mantiene pegadas
     * con paralaje, la vuelta a reposo y el truco de reanudar el giro desde
     * el angulo en el que se quedo.
     *
     * QUE se considera objetivo lo decide TARGETS: en el original hay que ir
     * poniendo la clase `cursor-target` a mano en el markup; aqui se reparte
     * por selector para no tocar sesenta sitios. Anadir mas es agregar una
     * linea a esa lista. */
    (() => {
        if (!window.gsap) { console.warn('TargetCursor: GSAP no disponible.'); return; }
        const gsap = window.gsap;

        const CONF = {
        targetSelector: '.cursor-target',
        spinDuration: 2,
        hideDefaultCursor: true,
        hoverDuration: 0.2,
        parallaxOn: true,
        cursorColor: '#ffffff',
        cursorColorOnTarget: undefined,
        };
        const CONST = { borderWidth: 3, cornerSize: 12 };

        /* Los elementos que el cursor encuadra. Botones, navegacion, campos
         * del formulario y la carpeta de planes; las superficies grandes
         * (lamas, tarjetas del abanico, mazo) se quedan fuera a proposito:
         * encuadrar una caja de 560px es mas ruido que senal, y ya tienen su
         * propio efecto al pasar por encima. */
        const TARGETS = [
        '.btn-primary', '.btn-ghost', '.btn-nav',
        '.gooey-nav-container li',
        '.lk-emerald .lkx-links a', '.lk-emerald .lkx-navend .lk-pill', '.lk-emerald .lkx-cta .lk-pill',
        '.lk-emerald .lk-brand',
        '#planFolder', '#submitBtn', '.cur-switch button', '.lk-social a',
        '#eventsCoin', '.ev-close', '.ev-nav',
        '.form-group input', '.form-group select', '.form-group textarea',
        '.social-links a', '.footer-col ul li a',
        ].join(',');

        /* ---- movil: el componente no se monta ------------------------- */
        const isMobile = (() => {
        const hasTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        const small = window.innerWidth <= 768;
        const ua = (navigator.userAgent || navigator.vendor || '').toLowerCase();
        const re = /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i;
        return (hasTouch && small) || re.test(ua);
        })();
        if (isMobile) return;
        if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        document.querySelectorAll(TARGETS).forEach((el) => el.classList.add('cursor-target'));

        /* ---- el cursor (el original lo mete por portal en el body) ----- */
        const cursor = document.createElement('div');
        cursor.className = 'target-cursor-wrapper';
        cursor.setAttribute('aria-hidden', 'true');
        cursor.style.setProperty('--tc-color', CONF.cursorColor);
        cursor.innerHTML =
        '<div class="target-cursor-dot"></div>' +
        '<div class="target-cursor-corner corner-tl"></div>' +
        '<div class="target-cursor-corner corner-tr"></div>' +
        '<div class="target-cursor-corner corner-br"></div>' +
        '<div class="target-cursor-corner corner-bl"></div>';
        document.body.appendChild(cursor);

        const dot = cursor.querySelector('.target-cursor-dot');
        const corners = Array.from(cursor.querySelectorAll('.target-cursor-corner'));

        if (CONF.hideDefaultCursor) document.documentElement.classList.add('tc-on');

        /* Un elemento `position: fixed` se situa respecto al viewport SALVO
         * que un ancestro cree un bloque contenedor (transform, perspective,
         * filter, will-change de esos, o contain). El cursor cuelga del
         * <body>, asi que hoy no hay ninguno, pero se mide igualmente: si
         * manana alguien le pone un transform al body, esto lo salva. */
        const getContainingBlock = (element) => {
        let node = element && element.parentElement;
        while (node && node !== document.documentElement) {
            const st = getComputedStyle(node);
            if (st.transform !== 'none' || st.perspective !== 'none' || st.filter !== 'none' ||
                st.willChange.includes('transform') || st.willChange.includes('perspective') ||
                st.willChange.includes('filter') || /paint|layout|strict|content/.test(st.contain)) return node;
            node = node.parentElement;
        }
        return null;
        };
        let block = getContainingBlock(cursor);
        const getOffset = () => {
        if (!block) return { x: 0, y: 0 };
        const r = block.getBoundingClientRect();
        return { x: r.left + block.clientLeft, y: r.top + block.clientTop };
        };

        const off0 = getOffset();
        gsap.set(cursor, {
        xPercent: -50, yPercent: -50,
        x: window.innerWidth / 2 - off0.x,
        y: window.innerHeight / 2 - off0.y,
        });

        /* ---- giro ------------------------------------------------------- */
        let spinTl = null;
        const createSpin = () => {
        if (spinTl) spinTl.kill();
        spinTl = gsap.timeline({ repeat: -1 })
            .to(cursor, { rotation: '+=360', duration: CONF.spinDuration, ease: 'none' });
        };
        createSpin();

        /* ---- enganche al objetivo ---------------------------------------- */
        let activeTarget = null, currentLeave = null, resumeTimeout = null;
        let targetCorners = null;
        const strength = { current: 0 };

        const tickerFn = () => {
        if (!targetCorners) return;
        const s = strength.current;
        if (s === 0) return;

        const cx = gsap.getProperty(cursor, 'x');
        const cy = gsap.getProperty(cursor, 'y');

        corners.forEach((corner, i) => {
            const curX = gsap.getProperty(corner, 'x');
            const curY = gsap.getProperty(corner, 'y');
            const tx = targetCorners[i].x - cx;
            const ty = targetCorners[i].y - cy;
            const fx = curX + (tx - curX) * s;
            const fy = curY + (ty - curY) * s;
            const dur = s >= 0.99 ? (CONF.parallaxOn ? 0.2 : 0) : 0.05;
            gsap.to(corner, { x: fx, y: fy, duration: dur, ease: dur === 0 ? 'none' : 'power1.out', overwrite: 'auto' });
        });
        };

        const moveCursor = (x, y) => {
        const o = getOffset();
        gsap.to(cursor, { x: x - o.x, y: y - o.y, duration: 0.1, ease: 'power3.out' });
        };
        addEventListener('mousemove', (e) => moveCursor(e.clientX, e.clientY));

        addEventListener('mousedown', () => {
        gsap.to(dot, { scale: 0.7, duration: 0.3 });
        gsap.to(cursor, { scale: 0.9, duration: 0.2 });
        });
        addEventListener('mouseup', () => {
        gsap.to(dot, { scale: 1, duration: 0.3 });
        gsap.to(cursor, { scale: 1, duration: 0.2 });
        });

        const cleanupTarget = (t) => {
        if (currentLeave && t) t.removeEventListener('mouseleave', currentLeave);
        currentLeave = null;
        };

        addEventListener('mouseover', (e) => {
        let node = e.target, found = null;
        while (node && node !== document.body) {
            if (node.matches && node.matches(CONF.targetSelector)) { found = node; break; }
            node = node.parentElement;
        }
        const target = found;
        if (!target || activeTarget === target) return;
        if (activeTarget) cleanupTarget(activeTarget);
        if (resumeTimeout) { clearTimeout(resumeTimeout); resumeTimeout = null; }

        activeTarget = target;
        corners.forEach((c) => gsap.killTweensOf(c, 'x,y'));
        gsap.killTweensOf(cursor, 'rotation');
        if (spinTl) spinTl.pause();
        gsap.set(cursor, { rotation: 0 });

        if (CONF.cursorColorOnTarget) {
            gsap.to(corners, { borderColor: CONF.cursorColorOnTarget, duration: 0.15, ease: 'power2.out' });
            gsap.to(dot, { backgroundColor: CONF.cursorColorOnTarget, duration: 0.15, ease: 'power2.out' });
        }

        const rect = target.getBoundingClientRect();
        const { borderWidth: bw, cornerSize: cs } = CONST;
        const o = getOffset();
        const cx = gsap.getProperty(cursor, 'x');
        const cy = gsap.getProperty(cursor, 'y');

        targetCorners = [
            { x: rect.left - bw - o.x,            y: rect.top - bw - o.y },
            { x: rect.right + bw - cs - o.x,      y: rect.top - bw - o.y },
            { x: rect.right + bw - cs - o.x,      y: rect.bottom + bw - cs - o.y },
            { x: rect.left - bw - o.x,            y: rect.bottom + bw - cs - o.y },
        ];

        gsap.ticker.add(tickerFn);
        gsap.to(strength, { current: 1, duration: CONF.hoverDuration, ease: 'power2.out' });

        corners.forEach((corner, i) => {
            gsap.to(corner, {
            x: targetCorners[i].x - cx, y: targetCorners[i].y - cy,
            duration: 0.2, ease: 'power2.out',
            });
        });

        const leaveHandler = () => {
            gsap.ticker.remove(tickerFn);
            targetCorners = null;
            gsap.set(strength, { current: 0, overwrite: true });
            activeTarget = null;

            if (CONF.cursorColorOnTarget) {
            gsap.to(corners, { borderColor: CONF.cursorColor, duration: 0.15, ease: 'power2.out' });
            gsap.to(dot, { backgroundColor: CONF.cursorColor, duration: 0.15, ease: 'power2.out' });
            }

            gsap.killTweensOf(corners, 'x,y');
            const { cornerSize: cs2 } = CONST;
            const rest = [
            { x: -cs2 * 1.5, y: -cs2 * 1.5 }, { x: cs2 * 0.5, y: -cs2 * 1.5 },
            { x: cs2 * 0.5, y: cs2 * 0.5 },   { x: -cs2 * 1.5, y: cs2 * 0.5 },
            ];
            const tl = gsap.timeline();
            corners.forEach((corner, i) => {
            tl.to(corner, { x: rest[i].x, y: rest[i].y, duration: 0.3, ease: 'power3.out' }, 0);
            });

            // Reanuda el giro desde el angulo en el que se quedo, para que no
            // pegue un salto al volver a girar.
            resumeTimeout = setTimeout(() => {
            if (!activeTarget && spinTl) {
                const rot = gsap.getProperty(cursor, 'rotation');
                const norm = rot % 360;
                spinTl.kill();
                spinTl = gsap.timeline({ repeat: -1 })
                .to(cursor, { rotation: '+=360', duration: CONF.spinDuration, ease: 'none' });
                gsap.to(cursor, {
                rotation: norm + 360,
                duration: CONF.spinDuration * (1 - norm / 360),
                ease: 'none',
                onComplete: () => spinTl && spinTl.restart(),
                });
            }
            resumeTimeout = null;
            }, 50);

            cleanupTarget(target);
        };

        currentLeave = leaveHandler;
        target.addEventListener('mouseleave', leaveHandler);
        }, { passive: true });

        /* Al hacer scroll el objetivo puede irse de debajo del cursor sin que
         * llegue a dispararse su mouseleave. */
        addEventListener('scroll', () => {
        if (!activeTarget) return;
        const o = getOffset();
        const mx = gsap.getProperty(cursor, 'x') + o.x;
        const my = gsap.getProperty(cursor, 'y') + o.y;
        const under = document.elementFromPoint(mx, my);
        const still = under && (under === activeTarget || under.closest(CONF.targetSelector) === activeTarget);
        if (!still && currentLeave) currentLeave();
        }, { passive: true });

        addEventListener('resize', () => { block = getContainingBlock(cursor); });
    })();
    </script>
        <script>
    /* ══════════════════════════════════════════════════════════════════
     * DRIFT WALL — puerto a JS plano
     * ══════════════════════════════════════════════════════════════════
     * La mecanica del componente, tal cual: reparto de teselas en columnas
     * por turno, copias suficientes de cada columna para que el bucle no se
     * vea, velocidad por columna con el factor pseudoaleatorio de la razon
     * aurea (columnFactor), signo alterno entre columnas, amortiguacion
     * exponencial tanto del paralaje (tau 0.12) como de la velocidad (0.28
     * en marcha, 0.16 al frenar), y el envoltorio modular del desplazamiento.
     *
     * Las teselas se declaran en ITEMS y apuntan a img/logos/: son los
     * originales de img/empresas/ ya sin fondo, recortados y en WebP con
     * alfa. Se regeneran con `python tools/clean_logos.py`. */
    (() => {
        const root = document.getElementById('cliWall');
        if (!root) return;

        const ITEMS = [
        { image: 'img/logos/AVV.webp', title: 'AVV' },
        { image: 'img/logos/Adivon.webp', title: 'Adivon' },
        { image: 'img/logos/AlyPeru.webp', title: 'Aly Peru' },
        { image: 'img/logos/BeatrizCampos.webp', title: 'Beatriz Campos' },
        { image: 'img/logos/BrisasMayo.webp', title: 'Brisas Mayo' },
        { image: 'img/logos/Buho.webp', title: 'Buho' },
        { image: 'img/logos/CamaraComercio.webp', title: 'Camara Comercio' },
        { image: 'img/logos/CasaDrywall.webp', title: 'Casa Drywall' },
        { image: 'img/logos/CebaPB.webp', title: 'Ceba PB' },
        { image: 'img/logos/CentralGas.webp', title: 'Central Gas' },
        { image: 'img/logos/Cetecop.webp', title: 'Cetecop' },
        { image: 'img/logos/Excelencia.webp', title: 'Excelencia' },
        { image: 'img/logos/Golem.webp', title: 'Golem' },
        { image: 'img/logos/IEP.webp', title: 'IEP' },
        { image: 'img/logos/IESAL.webp', title: 'IESAL' },
        { image: 'img/logos/JeanPiaget.webp', title: 'Jean Piaget' },
        { image: 'img/logos/Kasuva.webp', title: 'Kasuva' },
        { image: 'img/logos/Leon.webp', title: 'Leon' },
        { image: 'img/logos/Lions.webp', title: 'Lions' },
        { image: 'img/logos/MH.webp', title: 'MH' },
        { image: 'img/logos/MisPequegenios.webp', title: 'Mis Pequegenios' },
        { image: 'img/logos/Montessori.webp', title: 'Montessori' },
        { image: 'img/logos/NuevaEra.webp', title: 'Nueva Era' },
        { image: 'img/logos/Nutritec.webp', title: 'Nutritec' },
        { image: 'img/logos/Oikos.webp', title: 'Oikos' },
        { image: 'img/logos/ProfeNando.webp', title: 'Profe Nando' },
        { image: 'img/logos/RentalTech.webp', title: 'Rental Tech' },
        { image: 'img/logos/TicketWanka.webp', title: 'Ticket Wanka' },
        { image: 'img/logos/Ugarte.webp', title: 'Ugarte' },
        { image: 'img/logos/Virtus.webp', title: 'Virtus' },
        { image: 'img/logos/Zatu.webp', title: 'Zatu' },
        { image: 'img/logos/Zenith.webp', title: 'Zenith' },
        ];

        const CONF = {
        columns: 5,
        tileWidth: 200, tileHeight: 132, gap: 18, radius: 14,
        tilt: 16, turn: -14, roll: 0,
        perspective: 1200, depth: 120,
        speed: 42, direction: 'up', variance: 0.45,
        parallax: 0.6, pauseOnHover: false,
        lift: 64, fade: 0.6, dim: 0.55,
        grayscale: true,          // logos desaturados en reposo, color al pasar
        overlayColor: '#000000',  // #060010 en el original (demo violeta)
        };

        const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

        /* ---- variables CSS, como el cssVars del componente ------------- */
        const v = (k, val) => root.style.setProperty(k, val);
        v('--dw-tile-w', CONF.tileWidth + 'px'); v('--dw-tile-h', CONF.tileHeight + 'px');
        v('--dw-gap', CONF.gap + 'px'); v('--dw-radius', CONF.radius + 'px');
        v('--dw-perspective', CONF.perspective + 'px'); v('--dw-lift', CONF.lift + 'px');
        v('--dw-dim', String(CONF.dim)); v('--dw-gray', CONF.grayscale ? '1' : '0');
        v('--dw-overlay', CONF.overlayColor);
        v('--dw-edge', Math.max(0, (1 - CONF.fade) * 100) + '%');

        /* ---- verbatim ----------------------------------------------------- */
        const columnFactor = (index, variance) => {
        const pseudo = ((index * 0.6180339887 + 0.35) % 1) * 2 - 1;
        return 1 + variance * pseudo;
        };

        /* ---- reparto en columnas ------------------------------------------ */
        const columnItems = Array.from({ length: CONF.columns }, () => []);
        ITEMS.forEach((it, i) => columnItems[i % CONF.columns].push(it));
        columnItems.forEach((col, c) => { if (!col.length) columnItems[c] = ITEMS.slice(0, 1); });

        const unit = CONF.tileHeight + CONF.gap;
        let containerHeight = root.clientHeight || 600;
        let columnMeta = [];
        const computeMeta = () => {
        /* El componente hace `copies = max(2, ceil(alto*1.6/copyHeight)+1)`
         * por columna. Con 6-7 logos por columna eso da 2 copias, y falla
         * de dos maneras: la pista se desplaza hasta una copia entera y su
         * final asoma en la zona visible; y como el plano mide lo que la
         * columna MAS ALTA, las columnas con un logo menos (pista mas
         * corta) no llegan abajo. Se veia como huecos donde el puntero no
         * enganchaba nada.
         *
         * Aqui todas las pistas se llevan a una misma altura objetivo:
         * tres veces la copia mas alta mas 2.2 alturas de contenedor, que es
         * lo que garantiza cobertura para cualquier desplazamiento. */
        const heights = columnItems.map((col) => Math.max(unit, col.length * unit));
        const target = 3 * Math.max(...heights) + containerHeight * 2.2;
        columnMeta = heights.map((copyHeight) => ({
            copyHeight,
            copies: Math.max(2, Math.ceil(target / copyHeight)),
        }));
        };
        computeMeta();

        /* ---- DOM ---------------------------------------------------------- */
        const plane = document.createElement('div');
        plane.className = 'drift-wall__plane';
        root.appendChild(plane);

        const tracks = [];
        const build = () => {
        plane.innerHTML = '';
        tracks.length = 0;
        columnItems.forEach((col, c) => {
            const colEl = document.createElement('div'); colEl.className = 'drift-wall__col';
            const track = document.createElement('div'); track.className = 'drift-wall__track';
            for (let copy = 0; copy < columnMeta[c].copies; copy++) {
            col.forEach((item, k) => {
                const tile = document.createElement('div');
                tile.className = 'drift-wall__tile';
                tile.tabIndex = 0; tile.setAttribute('role', 'button');
                tile.setAttribute('aria-label', item.title || 'tile');
                tile.dataset.tileId = c + '-' + copy + '-' + k;
                tile.dataset.col = String(c);
                tile.innerHTML =
                '<span class="drift-wall__inner">' +
                '<img src="' + item.image + '" alt="' + (item.title || '') + '" loading="lazy" decoding="async" draggable="false">' +
                '<span class="drift-wall__overlay" aria-hidden="true"></span></span>';
                tile.addEventListener('focus', () => activate(tile.dataset.tileId, c));
                tile.addEventListener('blur', release);
                track.appendChild(tile);
            });
            }
            colEl.appendChild(track); plane.appendChild(colEl);
            tracks.push(track);
        });
        };

        /* ---- estado -------------------------------------------------------- */
        const dirSign = CONF.direction === 'up' ? 1 : -1;
        const baseVelocities = columnItems.map((_, c) =>
        CONF.speed * columnFactor(c, CONF.variance) * dirSign * (c % 2 === 0 ? 1 : -1));
        let offsets = columnMeta.map((m, c) => m.copyHeight * ((c * 0.37) % 1));
        let velocities = columnItems.map(() => 0);
        let hoveredCol = -1, wallHovered = false, activeId = null;
        const pointer = { x: 0, y: 0 }, damped = { x: 0, y: 0 };
        let lastTs = null;

        const applyPlaneTransform = (px, py) => {
        plane.style.transform =
            'translate(-50%, -50%) scale(1.18) ' +
            'rotateX(' + (CONF.tilt + py) + 'deg) rotateY(' + (CONF.turn + px) + 'deg) rotateZ(' + CONF.roll + 'deg) ' +
            'translateZ(' + (-CONF.depth) + 'px)';
        };

        const setActive = (id) => {
        activeId = id;
        plane.querySelectorAll('.drift-wall__tile.is-active').forEach((t) => t.classList.remove('is-active'));
        if (id) { const t = plane.querySelector('[data-tile-id="' + id + '"]'); if (t) t.classList.add('is-active'); }
        };
        const activate = (id, col) => { hoveredCol = col; setActive(id); };
        const release = () => { hoveredCol = -1; setActive(null); };
        // Construye DESPUES de definir los manejadores: build los pasa a
        // addEventListener y una const en zona muerta temporal revienta ahi.
        build();

        /* ---- bucle ---------------------------------------------------------- */
        const animate = (ts) => {
        if (lastTs === null) lastTs = ts;
        const dt = Math.min(0.05, Math.max(0, ts - lastTs) / 1000);
        lastTs = ts;

        const maxTilt = CONF.parallax * 8;
        const targetX = pointer.x * maxTilt, targetY = -pointer.y * maxTilt;
        const damp = 1 - Math.exp(-dt / 0.12);
        damped.x += (targetX - damped.x) * damp;
        damped.y += (targetY - damped.y) * damp;
        applyPlaneTransform(damped.x, damped.y);

        for (let c = 0; c < tracks.length; c++) {
            const meta = columnMeta[c]; if (!meta) continue;
            if (!reduced) {
            const paused = wallHovered && CONF.pauseOnHover;
            const factor = (paused || hoveredCol === c) ? 0 : 1;
            const target = baseVelocities[c] * factor;
            const ease = 1 - Math.exp(-dt / (target === 0 ? 0.16 : 0.28));
            velocities[c] += (target - velocities[c]) * ease;
            let next = (offsets[c] || 0) + velocities[c] * dt;
            next = ((next % meta.copyHeight) + meta.copyHeight) % meta.copyHeight;
            offsets[c] = next;
            }
            tracks[c].style.transform = 'translate3d(0, ' + (-(offsets[c] || 0)) + 'px, 0)';
        }
        requestAnimationFrame(animate);
        };
        requestAnimationFrame(animate);

        /* ---- puntero -------------------------------------------------------- */
        root.addEventListener('pointerenter', () => { wallHovered = true; });
        root.addEventListener('pointermove', (e) => {
        const rect = root.getBoundingClientRect();
        if (CONF.parallax > 0 && !reduced) {
            pointer.x = (e.clientX - rect.left) / rect.width - 0.5;
            pointer.y = (e.clientY - rect.top) / rect.height - 0.5;
        }
        const hit = document.elementFromPoint(e.clientX, e.clientY);
        const tile = hit && hit.closest ? hit.closest('[data-tile-id]') : null;
        if (!tile) return;
        const id = tile.dataset.tileId;
        if (id === activeId) return;
        activate(id, Number(tile.dataset.col));
        });
        root.addEventListener('pointerleave', () => {
        wallHovered = false; pointer.x = 0; pointer.y = 0; release();
        });

        /* El componente re-mide el contenedor con un ResizeObserver y
         * reconstruye las copias si cambia el alto. */
        new ResizeObserver((entries) => {
        const h = entries[0].contentRect.height || 600;
        if (Math.abs(h - containerHeight) < 1) return;
        containerHeight = h;
        computeMeta(); build();
        offsets = columnMeta.map((m, c) => m.copyHeight * ((c * 0.37) % 1));
        velocities = columnItems.map(() => 0);
        }).observe(root);
    })();
    </script>
        <script>
    /* ── Selector de moneda ──────────────────────────────────────────────
     * Los precios estan en los data-* de cada .plan-price; aqui solo se
     * cambia el simbolo y la cifra, con el formato de cada pais. La
     * eleccion se guarda en localStorage. Al cargar, si el navegador viene
     * en espanol de Colombia arranca en pesos; si no, en soles. */
    (() => {
        const sw = document.getElementById('curSwitch');
        if (!sw) return;
        const btns = Array.from(sw.querySelectorAll('[data-cur]'));
        const prices = Array.from(document.querySelectorAll('.plan-price[data-pen]'));

        const CUR = {
        pen: { sym: 'S/.', fmt: new Intl.NumberFormat('es-PE', { maximumFractionDigits: 0 }) },
        usd: { sym: 'US$', fmt: new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }) },
        cop: { sym: '$',   fmt: new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }) },
        };

        const apply = (cur, animate) => {
        if (!CUR[cur]) cur = 'pen';
        btns.forEach((b) => b.setAttribute('aria-checked', String(b.dataset.cur === cur)));
        prices.forEach((p) => {
            const swap = () => {
            p.dataset.cur = cur;
            p.querySelector('.plan-cur').textContent = CUR[cur].sym;
            p.querySelector('.plan-amt').textContent = CUR[cur].fmt.format(Number(p.dataset[cur]));
            p.classList.remove('is-swapping');
            };
            if (!animate) return swap();
            p.classList.add('is-swapping');
            setTimeout(swap, 180);
        });
        try { localStorage.setItem('estelar-cur', cur); } catch (e) { /* modo privado */ }
        };

        let initial = null;
        try { initial = localStorage.getItem('estelar-cur'); } catch (e) { /* idem */ }
        if (!initial) {
        const lang = (navigator.language || '').toLowerCase();
        initial = lang.endsWith('-co') ? 'cop' : 'pen';
        }
        apply(initial, false);

        btns.forEach((b) => b.addEventListener('click', () => apply(b.dataset.cur, true)));
    })();
    </script>
    </body>
    </html>