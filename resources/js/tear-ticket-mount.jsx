import { useState } from 'react';
import { createRoot } from 'react-dom/client';
import TearTicket from './components/TearTicket';
import './components/GoLeftTicket.css';

/**
 * Boleto "Go Left!!" de la landing. Unico punto de entrada React del
 * sitio -el resto de welcome.blade.php sigue siendo Blade + JS vanilla-.
 * Datos (nombre, fecha, descripcion, action del form, csrf) llegan por
 * data-* en el div #tearTicketRoot, ver el <script type="text/x-...">
 * no, en realidad via dataset directo en el HTML (mas simple).
 */
function GoLeftTicket({ data }) {
  const [torn, setTorn] = useState(false);
  const [copied, setCopied] = useState(false);

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

  return (
    <div className="gl-ticket-wrap">
      <TearTicket
        image={data.image}
        video={data.video || ''}
        imageAlt={`Evento ${data.nombre}`}
        orientation="horizontal"
        scrim
        imageRadius={12}
        onTear={() => setTorn(true)}
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
        background="#27272a"
        color="#f5f5f5"
        border
        borderWidth={1}
        recenter
        ariaLabel="Arrancar para inscribirte"
        stub={
          <div className="gl-stub">
            <span className="gl-stub-live">LIVE</span>
            <p className="gl-stub-cta">Arranca para reservar</p>
          </div>
        }
      >
        <div className="gl-body">
          <h3 className="gl-name">{data.nombre}</h3>
          <p className="gl-date">
            <i className="fas fa-calendar" /> {data.fecha}
          </p>
          <p className="gl-desc">{data.descripcion}</p>
        </div>
      </TearTicket>

      {/* Solo antes de arrancar el boleto -para compartir el evento sin
          tener que inscribirse primero-. Una vez arrancado, desaparece:
          ahi el formulario es lo unico que importa. */}
      {!torn && (
        <button type="button" className={`gl-copy${copied ? ' is-copied' : ''}`} onClick={copyLink}>
          <i className="fas fa-link" /> {copied ? '¡Enlace copiado!' : 'Copiar link para compartir'}
        </button>
      )}

      {torn && (
        <div className="gl-reveal">
          {data.action ? (
            <form method="POST" action={data.action} className="gl-form">
              <input type="hidden" name="_token" value={data.csrf} />
              <div className="gl-form-row">
                <div className="form-group">
                  <label>Nombre</label>
                  <input type="text" name="nombres" placeholder="Tu nombre" required maxLength={200} />
                </div>
                <div className="form-group">
                  <label>Celular</label>
                  <input type="tel" name="telefono" placeholder="+51 999 999 999" required maxLength={20} />
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
