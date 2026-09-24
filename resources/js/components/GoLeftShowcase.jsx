import { useEffect, useRef, useState } from 'react';
import { createPortal } from 'react-dom';
import TearTicket from './TearTicket';
import EventsCarousel from './EventsCarousel';
import PredictiveArc from './originkit/ui/predictive-arc';
import AsciiGlitchRipple from './AsciiGlitchRipple';
import './GoLeftTicket.css';

const SOON_SLIDES = [
  { id: 'soon-1', title: 'Próximamente' },
  { id: 'soon-2', title: 'Próximamente' },
  { id: 'soon-3', title: 'Próximamente' },
];

/**
 * Rafaga de confeti hecha a mano (canvas, sin libreria nueva) al
 * desglosar el ticket. Un solo estallido desde el centro con gravedad
 * simple, contado en frames (no en tiempo real) para no complicar el
 * ciclo de vida: 90 frames a ~60fps son ~1.5s, y el propio componente
 * se desmonta solo cuando `trigger` vuelve a false (ver onTear).
 */
function ConfettiBurst({ trigger }) {
  const canvasRef = useRef(null);

  useEffect(() => {
    if (!trigger) return undefined;
    const canvas = canvasRef.current;
    if (!canvas) return undefined;

    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    const w = window.innerWidth;
    const h = window.innerHeight;
    canvas.width = w * dpr;
    canvas.height = h * dpr;
    canvas.style.width = `${w}px`;
    canvas.style.height = `${h}px`;
    const ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);

    const colors = ['#73c6f5', '#e0263f', '#f5d442', '#ffffff', '#3ddc84'];
    const particles = Array.from({ length: 140 }, () => {
      const angle = Math.random() * Math.PI * 2;
      const speed = 3 + Math.random() * 8;
      return {
        x: w / 2,
        y: h / 2,
        vx: Math.cos(angle) * speed,
        vy: Math.sin(angle) * speed - 6,
        size: 7 + Math.random() * 8,
        color: colors[(Math.random() * colors.length) | 0],
        rot: Math.random() * Math.PI,
        spin: (Math.random() - 0.5) * 0.35,
      };
    });

    let frame = 0;
    let raf = 0;
    const totalFrames = 90;
    const tick = () => {
      frame += 1;
      ctx.clearRect(0, 0, w, h);
      const fade = Math.max(0, 1 - frame / totalFrames);
      particles.forEach(p => {
        p.vy += 0.35;
        p.x += p.vx;
        p.y += p.vy;
        p.rot += p.spin;
        ctx.save();
        ctx.globalAlpha = fade;
        ctx.translate(p.x, p.y);
        ctx.rotate(p.rot);
        ctx.fillStyle = p.color;
        ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
        ctx.restore();
      });
      if (frame < totalFrames) raf = requestAnimationFrame(tick);
    };
    raf = requestAnimationFrame(tick);
    return () => cancelAnimationFrame(raf);
  }, [trigger]);

  if (!trigger) return null;
  return <canvas ref={canvasRef} className="gls-confetti" aria-hidden="true" />;
}

function FieldError({ errors, name }) {
  const msg = errors?.[name]?.[0];
  if (!msg) return null;
  return <p className="text-red-400 text-xs mt-1.5">{msg}</p>;
}

/**
 * El input vuelve a ser nativo y simple -placeholder y tipeado son del
 * navegador otra vez, sin overlay-: el efecto AsciiGlitchRipple se
 * saca del texto del campo y pasa al LABEL ("NOMBRE COMPLETO" etc,
 * el "subtitulo" de cada campo), como <label> real (con `htmlFor`/
 * `id` para que siga funcionando el click-para-enfocar de un label
 * normal) envuelto por dentro.
 */
// `id`/`errorKey` opcionales: en la inscripcion por equipo el `name` es
// "miembros[0][nombres]" (asi lo arma FormData para Laravel), pero el
// error vuelve como "miembros.0.nombres" y el id tiene que ser unico
// por fila.
function Field({ label, name, id = name, errorKey = name, errors, ...inputProps }) {
  return (
    <div>
      <AsciiGlitchRipple as="label" htmlFor={id} className={labelCls}>
        {label}
      </AsciiGlitchRipple>
      <input id={id} name={name} className={inputCls} {...inputProps} />
      <FieldError errors={errors} name={errorKey} />
    </div>
  );
}

/**
 * Boton "Ver el tráiler" por evento -debajo de la seccion de eventos-.
 * Abre el mismo video que ya usa el ticket (data.video), pero aqui SIN
 * mutear: el click es un gesto del usuario, asi que el navegador deja
 * arrancar con audio (a diferencia de un autoplay de pagina, que si lo
 * bloquea).
 */
function TrailerButton({ title, video, onOpenChange }) {
  const [open, setOpen] = useState(false);
  if (!video) return null;

  const close = () => {
    setOpen(false);
    onOpenChange?.(false);
  };

  return (
    <>
      <button
        type="button"
        className="gls-trailer-btn cursor-target"
        onClick={() => {
          setOpen(true);
          onOpenChange?.(true);
        }}
      >
        <span className="gls-trailer-play"><i className="fas fa-play" /></span>
        <span>Ver el tráiler — {title}</span>
      </button>

      {open && createPortal(
        <div className="gls-trailer-overlay" onClick={close}>
          <button
            type="button"
            className="gls-trailer-close cursor-target"
            onClick={close}
            aria-label="Cerrar tráiler"
          >
            <i className="fas fa-times" />
          </button>
          <video
            src={video}
            controls
            autoPlay
            playsInline
            className="gls-trailer-video"
            onClick={e => e.stopPropagation()}
          />
        </div>,
        document.body
      )}
    </>
  );
}

const ROLES = ['Capitán', 'Jugador', 'Suplente'];

// Mismo tope que EventTeam::MAX_INTEGRANTES en el backend.
const MAX_MIEMBROS = 4;

let miembroSeq = 0;
function nuevoMiembro() {
  miembroSeq += 1;
  return { key: `m${miembroSeq}`, rol: '' };
}

/**
 * El <select> nativo del "Rol" nunca se pudo terminar de vestir con el
 * tema oscuro: el `<option>` acepta background-color/color por CSS,
 * pero el marco/sombra/tipografia del popup los pone el sistema
 * operativo, no el navegador -asi que siempre quedaba con un aire a
 * "por defecto" sin importar cuanto CSS se le pusiera-. Este reemplazo
 * es un boton + lista propios, con el mismo look que el resto de los
 * inputs. El <input type="hidden" name="rol"> es lo que en realidad
 * viaja en el FormData al enviar -onSubmit no cambia-, y `required`
 * vive en ese input tambien para que la validacion nativa del
 * navegador lo siga cubriendo.
 */
function RoleSelect({ value, onChange, errors, name = 'rol', id = 'rol', errorKey = name }) {
  const [open, setOpen] = useState(false);
  const rootRef = useRef(null);

  useEffect(() => {
    if (!open) return undefined;
    const onDocPointer = e => {
      if (rootRef.current && !rootRef.current.contains(e.target)) setOpen(false);
    };
    document.addEventListener('mousedown', onDocPointer);
    return () => document.removeEventListener('mousedown', onDocPointer);
  }, [open]);

  return (
    <div className={`gls-select${open ? ' is-open' : ''}`} ref={rootRef}>
      <input type="hidden" name={name} value={value} required />
      <button
        id={id}
        type="button"
        className={`gls-select-btn ${inputCls}`}
        onClick={() => setOpen(o => !o)}
        aria-haspopup="listbox"
        aria-expanded={open}
      >
        <span className={value ? '' : 'gls-select-placeholder'}>{value || 'Selecciona tu rol'}</span>
        <i className="fas fa-chevron-down" />
      </button>
      {open && (
        <ul className="gls-select-list" role="listbox">
          {ROLES.map(role => (
            <li key={role} role="option" aria-selected={value === role}>
              <button
                type="button"
                className={`gls-select-option cursor-target${value === role ? ' is-selected' : ''}`}
                onClick={() => {
                  onChange(role);
                  setOpen(false);
                }}
              >
                <span>{role}</span>
                {value === role && <i className="fas fa-check gls-select-check" aria-hidden="true" />}
              </button>
            </li>
          ))}
        </ul>
      )}
      <FieldError errors={errors} name={errorKey} />
    </div>
  );
}

const inputCls =
  'cursor-target w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm placeholder:text-slate-500 focus:border-sky-500 focus:ring-sky-500/30';
const labelCls = 'block text-xs font-medium text-slate-400 mb-1.5';

/**
 * `heroMode`:
 * - 'plain': banner cover a pantalla completa, con el titulo/fecha/CTA
 *   superpuestos (usado por la pagina standalone /eventos/{id}/vista).
 * - 'reveal': la imagen del evento entra como el slide "actual" de
 *   <EventsCarousel> -junto a las tarjetas "Proximamente"-, con el
 *   mismo efecto de recorte/revelado que el resto del carrusel. Ya no
 *   hay un hero aparte que se agranda con el scroll (HeroReveal se
 *   retiro) — usado por el panel deslizante del landing.
 */
function GoLeftShowcase({ data, heroMode = 'plain', onClose }) {
  // cta -> form -> ticket -> torn
  const [stage, setStage] = useState('cta');
  const [submitting, setSubmitting] = useState(false);
  const [errors, setErrors] = useState({});
  const [ticketData, setTicketData] = useState(null);
  const [confettiOn, setConfettiOn] = useState(false);
  const [ticketFalling, setTicketFalling] = useState(false);
  const [ticketGone, setTicketGone] = useState(false);
  const [activeSlide, setActiveSlide] = useState(0);
  // Inscripcion por equipo: 1 a MAX_MIEMBROS filas. Cada fila lleva un
  // `key` propio (no el indice) para que al quitar una del medio React
  // no le pase los valores tipeados a la de al lado.
  const [miembros, setMiembros] = useState(() => [nuevoMiembro()]);
  const [logoPreview, setLogoPreview] = useState(null);
  const formSectionRef = useRef(null);
  const audioRef = useRef(null);
  const [musicMuted, setMusicMuted] = useState(false);
  const [audioPlaying, setAudioPlaying] = useState(false);
  const [trailerOpen, setTrailerOpen] = useState(false);

  useEffect(() => () => { if (logoPreview) URL.revokeObjectURL(logoPreview); }, [logoPreview]);

  const setRolDe = (key, rol) => setMiembros(ms => ms.map(m => (m.key === key ? { ...m, rol } : m)));
  const agregarMiembro = () => setMiembros(ms => (ms.length < MAX_MIEMBROS ? [...ms, nuevoMiembro()] : ms));
  const quitarMiembro = key => setMiembros(ms => ms.filter(m => m.key !== key));

  const onLogoChange = e => {
    const file = e.target.files?.[0];
    setLogoPreview(file ? URL.createObjectURL(file) : null);
  };

  // El tema rojo/Left4Dead es del evento real (slide 0); en cuanto el
  // carrusel muestra una tarjeta "Proximamente" (sin evento ni color
  // propio todavia), todo vuelve al celeste/blanco de siempre.
  const isGoLeftTheme = heroMode === 'reveal' && activeSlide === 0;

  // Musica de fondo tipo "menu del juego" -solo mientras se ve el evento
  // real del torneo (isGoLeftTheme), nunca en las tarjetas "Proximamente"
  // ni en la pagina standalone-. Se pausa mientras el trailer esta abierto
  // para no pisar su propio audio. El click en la moneda que abre el panel
  // ya cuenta como gesto del usuario, asi que el navegador deja arrancar
  // con sonido -mismo motivo que el trailer, ver TrailerButton-.
  // EXCEPCION: si el evento tiene activado "Mostrar al ingresar a la web"
  // (data.autoOpen, ver go-left-panel-mount.jsx) el panel se abre solo, sin
  // click real -ahi el navegador bloquea el autoplay con sonido igual, sin
  // avisar. `audioPlaying` refleja el estado REAL (via onPlay/onPause del
  // <audio>, no la intencion) para que el icono no mienta, y el boton de
  // silenciar reintenta el play() directamente dentro de su propio click
  // -eso si cuenta como gesto- en vez de solo cambiar una bandera.
  useEffect(() => {
    const audio = audioRef.current;
    if (!audio) return;
    if (isGoLeftTheme && !trailerOpen && !musicMuted) {
      audio.play().catch(() => {});
    } else {
      audio.pause();
    }
  }, [isGoLeftTheme, trailerOpen, musicMuted]);

  const toggleMusic = () => {
    const audio = audioRef.current;
    if (!audio) return;
    if (audio.paused) {
      audio.play().catch(() => {});
      setMusicMuted(false);
    } else {
      audio.pause();
      setMusicMuted(true);
    }
  };

  useEffect(() => {
    if (stage === 'form') formSectionRef.current?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }, [stage]);

  const onSubmit = async e => {
    e.preventDefault();
    // "Rol" ya no es un <select> nativo (ver RoleSelect) — su input va
    // oculto, y `required` en un input hidden no lo valida el navegador
    // (queda fuera de la validacion nativa por spec). Se chequea a mano
    // para no depender del viaje al servidor para algo tan simple.
    const sinRol = {};
    miembros.forEach((m, i) => {
      if (!m.rol) sinRol[`miembros.${i}.rol`] = ['Selecciona un rol.'];
    });
    if (Object.keys(sinRol).length) {
      setErrors(sinRol);
      return;
    }
    setSubmitting(true);
    setErrors({});
    // FormData tal cual (multipart) -no JSON- porque puede llevar el
    // logo; Laravel arma el array `miembros` desde "miembros[0][...]".
    const payload = new FormData(e.target);
    if (!payload.get('logo')?.size) payload.delete('logo');
    try {
      const res = await fetch(data.actionEquipo, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': data.csrf,
        },
        body: payload,
      });
      if (res.status === 429) {
        setErrors({ _general: ['Demasiados intentos. Espera un minuto e intenta de nuevo.'] });
        return;
      }
      if (res.status === 422) {
        const body = await res.json();
        setErrors(body.errors || {});
        return;
      }
      if (!res.ok) throw new Error('request-failed');
      const body = await res.json();
      setTicketData(body);
      setStage('ticket');
    } catch {
      setErrors({ _general: ['No pudimos procesar tu inscripción. Intenta nuevamente.'] });
    } finally {
      setSubmitting(false);
    }
  };

  // Desglosar el ticket dispara el confeti y revela el comprobante YA,
  // sin esperar a nada mas. El propio ticket -ya inutil una vez que
  // esta el comprobante- cae de la pantalla en ese mismo instante y no
  // vuelve a aparecer, en paralelo, no antes.
  // Comprobante de pago: la inscripcion del equipo queda pendiente hasta
  // que el staff lo valide (ver EventTeamPagoController).
  const [comprobante, setComprobante] = useState({ estado: 'idle', error: null, nombre: null });

  const onComprobanteChange = async e => {
    const file = e.target.files?.[0];
    e.target.value = '';
    if (!file || !ticketData?.equipo?.comprobante_url) return;
    setComprobante({ estado: 'subiendo', error: null, nombre: file.name });
    const payload = new FormData();
    payload.append('comprobante', file);
    try {
      const res = await fetch(ticketData.equipo.comprobante_url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': data.csrf },
        body: payload,
      });
      const body = await res.json().catch(() => ({}));
      if (!res.ok) {
        const msg = body.errors?.comprobante?.[0] || body.message
          || 'No pudimos subir el comprobante. Intenta nuevamente.';
        setComprobante({ estado: 'error', error: msg, nombre: null });
        return;
      }
      setComprobante({ estado: 'enviado', error: null, nombre: file.name });
    } catch {
      setComprobante({ estado: 'error', error: 'No pudimos subir el comprobante. Intenta nuevamente.', nombre: null });
    }
  };

  const onTear = () => {
    setConfettiOn(true);
    setStage('torn');
    setTicketFalling(true);
    setTimeout(() => setTicketGone(true), 900);
    setTimeout(() => setConfettiOn(false), 1900);
  };

  const cta = stage === 'cta' && (
    <div className="gls-cta">
      <p className="gls-cta-question">¿Quieres participar?</p>
      <button type="button" className="gls-cta-btn cursor-target" onClick={() => setStage('form')}>
        Registrarme
      </button>
    </div>
  );

  return (
    <div className={`gls-page${heroMode === 'reveal' ? ' gls-page--arc' : ''}${isGoLeftTheme ? ' gls-page--accent-red' : ''}`}>
      {heroMode === 'reveal' && createPortal(
        <div className="gls-arc-bg" aria-hidden="true">
          <PredictiveArc
            background="#000000"
            baseColor={isGoLeftTheme ? '#4a0a0a' : '#ffffff'}
            accentColor={isGoLeftTheme ? '#e0263f' : '#ffffff'}
            highlight={isGoLeftTheme ? '#ff3b3b' : '#ffffff'}
            style={{ minWidth: 0, minHeight: 0 }}
          />
        </div>,
        document.body
      )}
      {heroMode === 'reveal' ? (
        <>
          <section className="gls-events-hero">
            <EventsCarousel
              variant="hero"
              theme={isGoLeftTheme ? 'left4dead' : undefined}
              onActiveChange={setActiveSlide}
              slides={[
                {
                  id: 'go-left',
                  title: data.nombre,
                  // El dorso de la card muestra el nombre completo del
                  // torneo; el frente/caption sigue con el slogan corto
                  // ("Go Left!!") que ya trae el arte del poster.
                  tituloDetalle: 'Torneo Left4Dead edición Estelar',
                  image: data.banner,
                  fecha: data.fecha,
                  descripcion: data.descripcion,
                  bases: data.bases,
                  // Siempre visible: la 1ra vez abre el formulario; si ya
                  // esta abierto (en celular queda fuera de pantalla) lleva
                  // hasta el en vez de desaparecer.
                  onRegister: stage === 'cta'
                    ? () => setStage('form')
                    : stage === 'form'
                      ? () => formSectionRef.current?.scrollIntoView({ behavior: 'smooth', block: 'start' })
                      : undefined,
                },
                ...SOON_SLIDES,
              ]}
            />
          </section>

          <section className="gls-trailers">
            <p className="gls-trailers-label">Tráilers</p>
            <div className="gls-trailers-list">
              <TrailerButton title={data.nombre} video={data.video} onOpenChange={setTrailerOpen} />
            </div>
          </section>
        </>
      ) : (
        <section className="gls-hero" style={{ backgroundImage: `url(${data.banner})` }}>
          <div className="gls-hero-scrim" />
          <div className="gls-hero-content">
            <p className="gls-hero-eyebrow">Eventos</p>
            <h1 className="gls-hero-title">{data.nombre}</h1>
            {data.fecha && (
              <p className="gls-hero-date">
                <i className="fas fa-calendar" /> {data.fecha}
              </p>
            )}
            {cta}
          </div>
        </section>
      )}

      {stage === 'form' && (
        <section className="gls-form-section" ref={formSectionRef}>
          <div className="gls-form-card">
            <p className="gls-form-title">Inscripción por equipo — {data.nombre}</p>

            <form className="space-y-4" onSubmit={onSubmit}>
              {/* ── Equipo: nombre + logo opcional, lado a lado ── */}
              <div className="gls-team-head">
                <div className="flex-1 min-w-0">
                  <Field
                    label="Nombre del equipo"
                    name="equipo"
                    placeholder="Nombre de tu team"
                    required
                    maxLength={150}
                    errors={errors}
                  />
                </div>
                <div className="gls-team-logo">
                  <span className={labelCls}>Logo <span className="gls-optional">(opcional)</span></span>
                  <label htmlFor="logo" className="gls-logo-drop cursor-target" title="Subir logo del equipo">
                    {logoPreview
                      ? <img src={logoPreview} alt="Logo del equipo" />
                      : <i className="fas fa-image" aria-hidden="true" />}
                  </label>
                  <input
                    id="logo"
                    name="logo"
                    type="file"
                    accept="image/png,image/jpeg,image/webp"
                    className="hidden"
                    onChange={onLogoChange}
                  />
                </div>
              </div>
              <FieldError errors={errors} name="logo" />

              {/* ── Integrantes: 1 a MAX_MIEMBROS ── */}
              {miembros.map((m, i) => {
                const n = `miembros[${i}]`;
                const k = `miembros.${i}`;
                const id = f => `${m.key}-${f}`;
                return (
                  <fieldset key={m.key} className="gls-member">
                    <legend className="gls-member-title">
                      Integrante {i + 1}{i === 0 && ' (tú)'}
                    </legend>
                    {i > 0 && (
                      <button
                        type="button"
                        className="gls-member-remove cursor-target"
                        onClick={() => quitarMiembro(m.key)}
                        aria-label={`Quitar integrante ${i + 1}`}
                      >
                        <i className="fas fa-times" />
                      </button>
                    )}

                    <Field
                      label="Nombre completo"
                      name={`${n}[nombres]`}
                      id={id('nombres')}
                      errorKey={`${k}.nombres`}
                      placeholder="Nombre completo"
                      required
                      maxLength={200}
                      errors={errors}
                    />

                    <div className="grid grid-cols-2 gap-3">
                      <Field label="Nickname" name={`${n}[nickname]`} id={id('nickname')} errorKey={`${k}.nickname`} placeholder="Nickname" required maxLength={100} errors={errors} />
                      <Field label="Edad" name={`${n}[edad]`} id={id('edad')} errorKey={`${k}.edad`} type="number" placeholder="Edad" required min={1} max={99} errors={errors} />
                    </div>

                    <div className="grid grid-cols-2 gap-3">
                      <Field label="WhatsApp" name={`${n}[telefono]`} id={id('telefono')} errorKey={`${k}.telefono`} type="tel" placeholder="+51 999 999 999" required maxLength={20} errors={errors} />
                      <Field label="Steam ID" name={`${n}[steam_id]`} id={id('steam_id')} errorKey={`${k}.steam_id`} placeholder="Steam ID" required maxLength={50} errors={errors} />
                    </div>

                    <div>
                      <AsciiGlitchRipple as="label" htmlFor={id('rol')} className={labelCls}>Rol</AsciiGlitchRipple>
                      <RoleSelect
                        value={m.rol}
                        onChange={rol => setRolDe(m.key, rol)}
                        errors={errors}
                        name={`${n}[rol]`}
                        id={id('rol')}
                        errorKey={`${k}.rol`}
                      />
                    </div>
                  </fieldset>
                );
              })}

              {miembros.length < MAX_MIEMBROS ? (
                <button type="button" className="gls-member-add cursor-target" onClick={agregarMiembro}>
                  <i className="fas fa-plus" /> Agregar integrante
                  <span className="gls-member-count">{miembros.length}/{MAX_MIEMBROS}</span>
                </button>
              ) : (
                <p className="gls-member-full">Equipo completo ({MAX_MIEMBROS}/{MAX_MIEMBROS})</p>
              )}

              {errors.miembros && <p className="text-red-400 text-sm">{errors.miembros[0]}</p>}
              {errors._general && <p className="text-red-400 text-sm">{errors._general[0]}</p>}

              <button type="submit" disabled={submitting} className="gls-submit cursor-target">
                {submitting ? 'Enviando…' : 'Inscribir equipo'}
              </button>
            </form>
          </div>
        </section>
      )}

      {(stage === 'ticket' || stage === 'torn') && createPortal(
        <div className="gls-ticket-overlay">
          <div className="gls-ticket-backdrop" />
          <div className="gls-ticket-stack">
            {!ticketGone && (
              <div className={`gls-ticket-float${ticketFalling ? ' is-falling' : ''}`}>
                <TearTicket
                  image={data.ticketImage}
                  video={data.video || ''}
                  imageAlt={`Evento ${data.nombre}`}
                  orientation="horizontal"
                  scrim={false}
                  imageRadius={12}
                  artSpan={1}
                  onTear={onTear}
                  width={720}
                  height={400}
                  stubSize={210}
                  radius={24}
                  holes={19}
                  holeSize={9}
                  notch={5}
                  roughness={0}
                  tearAngle={30}
                  stretch={46}
                  resistance={0.45}
                  rotate={4}
                  tilt
                  tiltMax={8}
                  tiltReach={400}
                  parallax={9}
                  perspective={1300}
                  background="#000000"
                  color="#f5f5f5"
                  border
                  borderWidth={1}
                  recenter
                  ariaLabel="Arrancar para ver tu comprobante"
                  stub={
                    <div className="gl-stub">
                      <span className="gl-stub-live">¡Desglosa para terminar!</span>
                    </div>
                  }
                />
              </div>
            )}

            {stage === 'torn' && ticketData && (
              <div className="gls-receipt">
                <p className="gls-receipt-title">
                  {ticketData.equipo ? '¡Solicitud recibida!' : '¡Listo! Ya procesamos tu solicitud'}
                </p>
                {ticketData.equipo && (
                  <p className="gls-receipt-pending">
                    <i className="fas fa-clock" /> Tu inscripción se confirma cuando validemos el pago.
                  </p>
                )}
                <div className="gls-receipt-qr" dangerouslySetInnerHTML={{ __html: ticketData.qr_svg }} />
                <p className="gls-receipt-code">{ticketData.codigo}</p>
                {ticketData.equipo && (
                  <div className="gls-receipt-team">
                    <div className="gls-receipt-team-head">
                      {ticketData.equipo.logo_url && <img src={ticketData.equipo.logo_url} alt="" />}
                      <span>{ticketData.equipo.nombre}</span>
                    </div>
                    <ul>
                      {ticketData.integrantes.map(a => (
                        <li key={a.codigo}>
                          <span className="gls-receipt-team-nick">{a.nickname}</span>
                          <span className="gls-receipt-team-rol">{a.rol}</span>
                          <a href={a.ticket_url} target="_blank" rel="noopener" className="cursor-target">
                            {a.codigo}
                          </a>
                        </li>
                      ))}
                    </ul>
                  </div>
                )}
                {ticketData.equipo && (
                  <div className={`gls-receipt-pay is-${comprobante.estado}`}>
                    {comprobante.estado === 'enviado' ? (
                      <>
                        <p className="gls-receipt-pay-ok">
                          <i className="fas fa-circle-check" /> Comprobante enviado — en revisión
                        </p>
                        <p className="gls-receipt-pay-file">{comprobante.nombre}</p>
                        <label htmlFor="glsComprobante" className="gls-receipt-pay-change cursor-target">
                          Cambiar archivo
                        </label>
                      </>
                    ) : (
                      <label htmlFor="glsComprobante" className="gls-receipt-pay-btn cursor-target">
                        {comprobante.estado === 'subiendo' ? (
                          <><i className="fas fa-spinner fa-spin" /> Subiendo…</>
                        ) : (
                          <><i className="fas fa-upload" /> Subir comprobante de inscripción</>
                        )}
                      </label>
                    )}
                    <input
                      id="glsComprobante"
                      type="file"
                      accept="image/png,image/jpeg,image/webp,application/pdf"
                      className="hidden"
                      disabled={comprobante.estado === 'subiendo'}
                      onChange={onComprobanteChange}
                    />
                    {comprobante.estado !== 'enviado' && (
                      <p className="gls-receipt-pay-hint">Yape, Plin o transferencia · imagen o PDF, máx. 5 MB</p>
                    )}
                    {comprobante.error && <p className="gls-receipt-pay-error">{comprobante.error}</p>}
                  </div>
                )}
                <div className="gls-receipt-actions">
                  <a
                    href={ticketData.whatsapp_url}
                    target="_blank"
                    rel="noopener"
                    className="cursor-target flex-1 py-3 rounded-xl text-sm font-semibold text-white text-center bg-emerald-600 hover:bg-emerald-500 shadow-[0_0_18px_rgba(16,185,129,0.35)] hover:shadow-[0_0_28px_rgba(16,185,129,0.55)] transition-all duration-200 active:scale-[0.98] inline-flex items-center justify-center gap-2"
                  >
                    <i className="fab fa-whatsapp text-base" />
                    Contactar por WhatsApp
                  </a>
                  <a
                    href={ticketData.ticket_url}
                    target="_blank"
                    rel="noopener"
                    className="cursor-target flex-1 py-3 rounded-xl text-sm font-semibold text-white text-center bg-gradient-to-r from-sky-500 to-cyan-500 shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)] transition-all duration-200 active:scale-[0.98]"
                  >
                    Guardar / imprimir
                  </a>
                  <button type="button" onClick={onClose} className="gls-receipt-back cursor-target">
                    <i className="fas fa-arrow-left text-xs" />
                    Volver
                  </button>
                </div>
              </div>
            )}
          </div>
        </div>,
        document.body
      )}

      {heroMode === 'reveal' && data.audio && createPortal(
        <>
          <audio
            ref={audioRef}
            src={data.audio}
            loop
            preload="none"
            onPlay={() => setAudioPlaying(true)}
            onPause={() => setAudioPlaying(false)}
          />
          {isGoLeftTheme && (
            <button
              type="button"
              className="gls-music-toggle cursor-target"
              onClick={toggleMusic}
              aria-label={audioPlaying ? 'Silenciar música' : 'Activar música'}
              title={audioPlaying ? 'Silenciar música' : 'Activar música'}
            >
              <i className={`fas ${audioPlaying ? 'fa-volume-high' : 'fa-volume-xmark'}`} />
            </button>
          )}
        </>,
        document.body
      )}

      {createPortal(<ConfettiBurst trigger={confettiOn} />, document.body)}
    </div>
  );
}

export default GoLeftShowcase;
