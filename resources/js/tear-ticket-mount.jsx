import { useEffect, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { createPortal } from 'react-dom';
import TearTicket from './components/TearTicket';
import './components/GoLeftTicket.css';

/**
 * Poster del evento: la imagen sola, clickeable. El hover se detecta desde
 * aqui mismo (`onHover` sube el estado al padre) para disparar el video de
 * fondo, ver GoLeftBgVideo.
 */
function GoLeftFront({ image, alt, onHover }) {
  return (
    <div className="gl-front" onMouseEnter={() => onHover(true)} onMouseLeave={() => onHover(false)}>
      <img src={image} alt={alt} draggable={false} />
    </div>
  );
}

/**
 * El video del evento como fondo de pantalla difuminado -no dentro de
 * la card, "de fondo de pantalla"-, al pasar el mouse sobre ella.
 * Se porta a #evVideoBg (hermano de .ev-backdrop en el HTML vanilla,
 * ver welcome.blade.php): un position:fixed dentro de #tearTicketRoot
 * terminaria posicionado contra .ev-card -que CardSwap transforma con
 * GSAP-, no contra el viewport real. Portado ahi afuera, es un fixed
 * de verdad sobre toda la pantalla.
 */
function GoLeftBgVideo({ video, active }) {
  const videoRef = useRef(null);
  const target = typeof document !== 'undefined' ? document.getElementById('evVideoBg') : null;

  useEffect(() => {
    const el = videoRef.current;
    if (!el) return;
    if (active) {
      el.currentTime = 0;
      el.play().catch(() => {});
    } else {
      el.pause();
    }
  }, [active]);

  if (!target || !video) return null;
  return createPortal(
    <video
      ref={videoRef}
      className={`ev-video-bg-el${active ? ' is-active' : ''}`}
      src={video}
      muted
      loop
      playsInline
      preload="none"
      aria-hidden="true"
    />,
    target
  );
}

/**
 * Boleto "Go Left!!" de la landing. Unico punto de entrada React del
 * sitio -el resto de welcome.blade.php sigue siendo Blade + JS vanilla-.
 * Datos (nombre, fecha, descripcion, action del form, csrf) llegan por
 * data-* en el div #tearTicketRoot.
 *
 * Flujo: una sola tarjeta visible a la vez, en secuencia -
 * poster del evento -> click entra al TearTicket -> arrancar el boleto
 * revela el detalle del evento (Registrarse/Copiar enlace) -> "Registrarse"
 * revela el formulario real de inscripcion al torneo. `step` es la unica
 * fuente de verdad de cual de las cuatro se muestra; nunca hay dos a la vez.
 */
function GoLeftTicket({ data }) {
  const [step, setStep] = useState('poster'); // 'poster' | 'ticket' | 'info' | 'form'
  const [copied, setCopied] = useState(false);
  const [frontHover, setFrontHover] = useState(false);

  const copyLink = async () => {
    try {
      await navigator.clipboard.writeText(window.location.href);
    } catch {
      const tmp = document.createElement('textarea');
      tmp.value = window.location.href;
      tmp.style.position = 'fixed';
      tmp.style.opacity = '0';
      document.body.appendChild(tmp);
      tmp.select();
      document.execCommand('copy');
      document.body.removeChild(tmp);
    }
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const onPosterKey = e => {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    e.preventDefault();
    setStep('ticket');
  };

  return (
    <div className="gl-ticket-wrap">
      {step === 'poster' && (
        <div
          className="gl-poster"
          role="button"
          tabIndex={0}
          aria-label={`Ver boleto del evento ${data.nombre}`}
          onClick={() => setStep('ticket')}
          onKeyDown={onPosterKey}
        >
          <GoLeftFront image={data.image} alt={`Evento ${data.nombre}`} onHover={setFrontHover} />
        </div>
      )}

      <GoLeftBgVideo video={data.video} active={frontHover} />

      {step === 'ticket' && (
        <div className="gl-ticket-enter">
          <TearTicket
            image={data.ticketImage || data.image}
            video={data.video || ''}
            imageAlt={`Evento ${data.nombre}`}
            orientation="horizontal"
            scrim={false}
            imageRadius={12}
            artSpan={1}
            onTear={() => setStep('info')}
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
            ariaLabel="Arrancar para inscribirte"
            stub={
              <div className="gl-stub">
                <span className="gl-stub-live">LIVE</span>
                <p className="gl-stub-cta">Arranca para inscribirte</p>
              </div>
            }
          />
        </div>
      )}

      {step === 'info' && (
        <div className="gl-back">
          <div className="gl-back-body">
            <h3 className="gl-name">{data.nombre}</h3>
            <p className="gl-date">
              <i className="fas fa-calendar" /> {data.fecha}
            </p>
            <p className="gl-desc gl-desc--back">{data.descripcion}</p>
          </div>
          <div className="gl-back-actions">
            <button type="button" className="gl-back-cta" onClick={() => setStep('form')}>
              Registrarse
            </button>
            <button
              type="button"
              className={`gl-back-link${copied ? ' is-copied' : ''}`}
              onClick={copyLink}
            >
              <i className="fas fa-link" /> {copied ? '¡Enlace copiado!' : 'Copiar enlace'}
            </button>
          </div>
        </div>
      )}

      {step === 'form' && (
        <div className="gl-reveal">
          {data.action ? (
            <form method="POST" action={data.action} className="gl-form">
              <input type="hidden" name="_token" value={data.csrf} />
              <p className="gl-form-title">Inscripción — {data.nombre}</p>

              <div className="form-group">
                <label>Nombre completo</label>
                <input type="text" name="nombres" placeholder="Tu nombre completo" required maxLength={200} />
              </div>

              <div className="gl-form-row">
                <div className="form-group">
                  <label>Nickname</label>
                  <input type="text" name="nickname" placeholder="Tu nickname" required maxLength={100} />
                </div>
                <div className="form-group">
                  <label>Edad</label>
                  <input type="number" name="edad" placeholder="Edad" required min={1} max={99} />
                </div>
              </div>

              <div className="gl-form-row">
                <div className="form-group">
                  <label>WhatsApp</label>
                  <input type="tel" name="telefono" placeholder="+51 999 999 999" required maxLength={20} />
                </div>
                <div className="form-group">
                  <label>Steam ID</label>
                  <input type="text" name="steam_id" placeholder="Tu Steam ID" required maxLength={50} />
                </div>
              </div>

              <div className="gl-form-row">
                <div className="form-group">
                  <label>Nombre del equipo</label>
                  <input type="text" name="equipo" placeholder="Nombre del equipo" required maxLength={150} />
                </div>
                <div className="form-group">
                  <label>Rol</label>
                  <select name="rol" required defaultValue="">
                    <option value="" disabled>Selecciona tu rol</option>
                    <option value="Capitán">Capitán</option>
                    <option value="Jugador">Jugador</option>
                    <option value="Suplente">Suplente</option>
                  </select>
                </div>
              </div>

              <button type="submit" className="gl-submit">Reservar mi lugar</button>
            </form>
          ) : (
            <p className="gl-pending">Las inscripciones abren pronto.</p>
          )}
        </div>
      )}
    </div>
  );
}

const root = document.getElementById('tearTicketRoot');
if (root) {
  createRoot(root).render(<GoLeftTicket data={{ ...root.dataset }} />);
}
