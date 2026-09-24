import { useEffect, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';
import GoLeftShowcase from './components/GoLeftShowcase';

const TRANSITION_MS = 520;

/**
 * Panel que cubre el landing deslizandose desde la derecha. No es un
 * overlay `position:fixed` con scroll propio -eso dejaria el scroll
 * atrapado dentro suyo (el formulario, dentro de GoLeftShowcase, hace
 * scrollIntoView contra `window` al pasar a stage='form')-: al abrirse
 * oculta `.main-container` (clase `events-open` en <body>, ver
 * GoLeftTicket.css) y queda como el unico contenido del documento, en
 * flujo normal, para que `window` sea el scroller real.
 */
function EventsPanel({ data }) {
  const [phase, setPhase] = useState('closed'); // closed | entering | open | leaving
  const savedScrollY = useRef(0);

  useEffect(() => {
    const onOpen = () => {
      if (phase !== 'closed') return;
      savedScrollY.current = window.scrollY;
      document.body.classList.add('events-open');
      window.scrollTo(0, 0);
      setPhase('entering');
    };
    document.addEventListener('eventos:open', onOpen);
    return () => document.removeEventListener('eventos:open', onOpen);
  }, [phase]);

  // "Mostrar al ingresar a la web" (toggle en Editar evento): abre el
  // panel apenas monta. Va despues del effect que registra el listener
  // de 'eventos:open' para que el evento ya tenga quien lo escuche.
  useEffect(() => {
    if (data.autoOpen === '1') {
      document.dispatchEvent(new CustomEvent('eventos:open'));
    }
  }, []);

  useEffect(() => {
    if (phase !== 'entering') return undefined;
    const raf1 = requestAnimationFrame(() => {
      requestAnimationFrame(() => setPhase('open'));
    });
    return () => cancelAnimationFrame(raf1);
  }, [phase]);

  useEffect(() => {
    if (phase !== 'leaving') return undefined;
    const t = setTimeout(() => {
      document.body.classList.remove('events-open');
      window.scrollTo(0, savedScrollY.current);
      setPhase('closed');
    }, TRANSITION_MS);
    return () => clearTimeout(t);
  }, [phase]);

  useEffect(() => {
    if (phase === 'closed') return undefined;
    const onKey = e => {
      if (e.key === 'Escape') setPhase(p => (p === 'open' || p === 'entering' ? 'leaving' : p));
    };
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [phase]);

  if (phase === 'closed') return null;

  const close = () => setPhase(p => (p === 'open' || p === 'entering' ? 'leaving' : p));

  return (
    <>
      <button type="button" className="events-panel__close cursor-target" onClick={close} aria-label="Cerrar eventos">
        <i className="fas fa-times" aria-hidden="true" />
      </button>
      <div className={`events-panel${phase === 'open' ? ' is-open' : ''}`}>
        <GoLeftShowcase data={data} heroMode="reveal" onClose={close} />
      </div>
    </>
  );
}

const root = document.getElementById('eventsPanelRoot');
if (root) {
  createRoot(root).render(<EventsPanel data={{ ...root.dataset }} />);
}
