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
function Field({ label, name, errors, ...inputProps }) {
  return (
    <div>
      <AsciiGlitchRipple as="label" htmlFor={name} className={labelCls}>
        {label}
      </AsciiGlitchRipple>
      <input id={name} name={name} className={inputCls} {...inputProps} />
      <FieldError errors={errors} name={name} />
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
function TrailerButton({ title, video }) {
  const [open, setOpen] = useState(false);
  if (!video) return null;

  return (
    <>
      <button type="button" className="gls-trailer-btn cursor-target" onClick={() => setOpen(true)}>
        <span className="gls-trailer-play"><i className="fas fa-play" /></span>
        <span>Ver el tráiler — {title}</span>
      </button>

      {open && createPortal(
        <div className="gls-trailer-overlay" onClick={() => setOpen(false)}>
          <button
            type="button"
            className="gls-trailer-close cursor-target"
            onClick={() => setOpen(false)}
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
function RoleSelect({ value, onChange, errors }) {
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
      <input type="hidden" name="rol" value={value} required />
      <button
        id="rol"
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
      <FieldError errors={errors} name="rol" />
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
  const [rol, setRol] = useState('');
  const formSectionRef = useRef(null);

  // El tema rojo/Left4Dead es del evento real (slide 0); en cuanto el
  // carrusel muestra una tarjeta "Proximamente" (sin evento ni color
  // propio todavia), todo vuelve al celeste/blanco de siempre.
  const isGoLeftTheme = heroMode === 'reveal' && activeSlide === 0;

  useEffect(() => {
    if (stage === 'form') formSectionRef.current?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }, [stage]);

  const onSubmit = async e => {
    e.preventDefault();
    // "Rol" ya no es un <select> nativo (ver RoleSelect) — su input va
    // oculto, y `required` en un input hidden no lo valida el navegador
    // (queda fuera de la validacion nativa por spec). Se chequea a mano
    // para no depender del viaje al servidor para algo tan simple.
    if (!rol) {
      setErrors({ rol: ['Selecciona un rol.'] });
      return;
    }
    setSubmitting(true);
    setErrors({});
    const payload = Object.fromEntries(new FormData(e.target).entries());
    try {
      const res = await fetch(data.action, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': data.csrf,
        },
        body: JSON.stringify(payload),
      });
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
                { id: 'go-left', title: data.nombre, image: data.banner, fecha: data.fecha, descripcion: data.descripcion, bases: data.bases },
                ...SOON_SLIDES,
              ]}
            />
            <div className="gls-hero-content">{cta}</div>
          </section>

          <section className="gls-trailers">
            <p className="gls-trailers-label">Tráilers</p>
            <div className="gls-trailers-list">
              <TrailerButton title={data.nombre} video={data.video} />
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
            <p className="gls-form-title">Inscripción — {data.nombre}</p>

            <form className="space-y-4" onSubmit={onSubmit}>
              <Field
                label="Nombre completo"
                name="nombres"
                placeholder="Tu nombre completo"
                required
                maxLength={200}
                errors={errors}
              />

              <div className="grid grid-cols-2 gap-3">
                <Field label="Nickname" name="nickname" placeholder="Tu nickname" required maxLength={100} errors={errors} />
                <Field label="Edad" name="edad" type="number" placeholder="Edad" required min={1} max={99} errors={errors} />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <Field
                  label="WhatsApp"
                  name="telefono"
                  type="tel"
                  placeholder="+51 999 999 999"
                  required
                  maxLength={20}
                  errors={errors}
                />
                <Field label="Steam ID" name="steam_id" placeholder="Tu Steam ID" required maxLength={50} errors={errors} />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <Field
                  label="Nombre del equipo"
                  name="equipo"
                  placeholder="Nombre del equipo"
                  required
                  maxLength={150}
                  errors={errors}
                />
                <div>
                  <AsciiGlitchRipple as="label" htmlFor="rol" className={labelCls}>Rol</AsciiGlitchRipple>
                  <RoleSelect value={rol} onChange={setRol} errors={errors} />
                </div>
              </div>

              {errors._general && <p className="text-red-400 text-sm">{errors._general[0]}</p>}

              <button type="submit" disabled={submitting} className="gls-submit cursor-target">
                {submitting ? 'Enviando…' : 'Reservar mi lugar'}
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
                <p className="gls-receipt-title">¡Listo! Ya procesamos tu solicitud</p>
                <div className="gls-receipt-qr" dangerouslySetInnerHTML={{ __html: ticketData.qr_svg }} />
                <p className="gls-receipt-code">{ticketData.codigo}</p>
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

      {createPortal(<ConfettiBurst trigger={confettiOn} />, document.body)}
    </div>
  );
}

export default GoLeftShowcase;
