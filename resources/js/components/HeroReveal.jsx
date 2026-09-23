import { useEffect, useState } from 'react';
import { motion, useReducedMotion } from 'motion/react';
import './HeroReveal.css';

/**
 * SIN USO: GoLeftShowcase.jsx (heroMode="reveal") ya no monta este
 * componente — el hero principal pasa a ser <EventsCarousel
 * variant="hero">, que junta el evento real y las tarjetas
 * "Proximamente" en un solo carrusel (mismo pedido: "elimina el
 * efecto de agrandado"). Se conserva el archivo sin borrar.
 *
 * Reemplazaba a ScrollExpand (retirado antes que este): en vez de
 * escrubear el clip-path cuadro a cuadro contra el scroll, arrancaba
 * con la imagen encogida y redondeada -inset()- y, apenas el usuario
 * empezaba a bajar, disparaba UNA sola transicion con resorte
 * (motion/react) hasta cubrir toda la pantalla. Adaptado de un
 * carrusel (Skiper 54 / Carousel_006) que animaba `current === index`;
 * aqui no habia slides, asi que ese mismo booleano era "revealed".
 *
 * La imagen real es retrato (1080x1440) y usaba `object-fit:contain`
 * para verse completa de arriba a abajo sin recortes; el fondo -detras,
 * `.hr-backdrop`- era la misma imagen borrosa y ampliada (cover) con un
 * Ken Burns lento, asi los costados no quedaban en negro.
 */
function HeroReveal({ src, title, scrollHint }) {
  const [revealed, setRevealed] = useState(false);
  const reduce = useReducedMotion();

  useEffect(() => {
    if (revealed) return undefined;
    const onScroll = () => {
      if (window.scrollY > 30) setRevealed(true);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, [revealed]);

  return (
    <section className="hr-stage">
      <motion.div
        className="hr-frame"
        initial={false}
        animate={{ clipPath: revealed ? 'inset(0% 0% 0% 0% round 0px)' : 'inset(14% 9% 14% 9% round 32px)' }}
        transition={reduce ? { duration: 0 } : { duration: 0.9, ease: [0.22, 1, 0.36, 1] }}
      >
        <div className="hr-backdrop" style={{ backgroundImage: `url(${src})` }} aria-hidden="true" />
        <img src={src} alt="" className="hr-image" draggable={false} />
        <div className="hr-scrim" />
      </motion.div>

      {title && (
        <motion.h1
          className="hr-title"
          initial={false}
          animate={{ opacity: revealed ? 0 : 1, y: revealed ? -24 : 0 }}
          transition={{ duration: 0.5 }}
        >
          {title}
        </motion.h1>
      )}
      {scrollHint && (
        <motion.p
          className="hr-hint"
          initial={false}
          animate={{ opacity: revealed ? 0 : 1, y: revealed ? 8 : 0 }}
          transition={{ duration: 0.4 }}
        >
          {scrollHint}
        </motion.p>
      )}
    </section>
  );
}

export default HeroReveal;
